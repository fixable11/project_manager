<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Exception;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_projects")
 */
class Project
{
    /**
     * @var Id $id Id.
     * @ORM\Column(type="work_projects_project_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $name Name.
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var integer $sort Sort.
     *
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @var Status $status Status vo.
     *
     * @ORM\Column(type="work_projects_project_status", length=16)
     */
    private $status;

    /**
     * @var ArrayCollection|Department[]
     * @ORM\OneToMany(
     *     targetEntity="App\Model\Work\Entity\Projects\Project\Department\Department",
     *     mappedBy="project", orphanRemoval=true, cascade={"all"}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $departments;

    /**
     * @var ArrayCollection|Membership[]
     * @ORM\OneToMany(targetEntity="Membership", mappedBy="project", orphanRemoval=true, cascade={"all"})
     */
    private $memberships;

    /**
     * Project constructor.
     *
     * @param Id      $id   Id vo.
     * @param string  $name Name.
     * @param integer $sort Sort type.
     */
    public function __construct(Id $id, string $name, int $sort)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = Status::active();
        $this->departments = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    /**
     * @param string  $name Name.
     * @param integer $sort Sort type.
     */
    public function edit(string $name, int $sort): void
    {
        $this->name = $name;
        $this->sort = $sort;
    }

    /**
     * Archive project.
     */
    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new DomainException('Project is already archived.');
        }
        $this->status = Status::archived();
    }

    /**
     * Reinstate project.
     */
    public function reinstate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Project is already active.');
        }
        $this->status = Status::active();
    }

    /**
     * Check if project archived.
     *
     * @return boolean
     */
    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return integer
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * Get status.
     *
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    public function addDepartment(DepartmentId $id, string $name): void
    {
        foreach ($this->departments as $department) {
            if ($department->isNameEqual($name)) {
                throw new DomainException('Department already exists.');
            }
        }

        $this->departments->add(new Department($this, $id, $name));
    }

    public function editDepartment(DepartmentId $id, string $name): void
    {
        foreach ($this->departments as $current) {
            if ($current->getId()->isEqual($id)) {
                $current->edit($name);
                return;
            }
        }

        throw new DomainException('Department is not found.');
    }

    /**
     * Remove department.
     *
     * @param DepartmentId $id Department id.
     *
     * @throws DomainException DomainException.
     */
    public function removeDepartment(DepartmentId $id): void
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                foreach ($this->memberships as $membership) {
                    if ($membership->isForDepartment($id)) {
                        throw new DomainException('Unable to remove department with members.');
                    }
                }
                $this->departments->removeElement($department);
                return;
            }
        }

        throw new DomainException('Department is not found.');
    }

    /**
     * Add member to project.
     *
     * @param Member $member
     * @param DepartmentId[] $departmentIds
     * @param Role[] $roles
     * @throws Exception
     */
    public function addMember(Member $member, array $departmentIds, array $roles): void
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($member->getId())) {
                throw new DomainException('Member already exists.');
            }
        }
        $departments = array_map([$this, 'getDepartment'], $departmentIds);
        $this->memberships->add(new Membership($this, $member, $departments, $roles));
    }

    /**
     * Edit member.
     *
     * @param MemberId $member              Member id.
     * @param DepartmentId[] $departmentIds Departments id.
     * @param Role[] $roles                 Project roles.
     *
     * @throws DomainException DomainException.
     */
    public function editMember(MemberId $member, array $departmentIds, array $roles): void
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($member)) {
                $membership->changeDepartments(array_map([$this, 'getDepartment'], $departmentIds));
                $membership->changeRoles($roles);
                return;
            }
        }
        throw new DomainException('Member is not found.');
    }

    /**
     * Remove member.
     *
     * @param MemberId $member Member id.
     */
    public function removeMember(MemberId $member): void
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($member)) {
                $this->memberships->removeElement($membership);
                return;
            }
        }
        throw new DomainException('Member is not found.');
    }

    /**
     * Get memberships.
     *
     * @return array
     */
    public function getMemberships()
    {
        return $this->memberships->toArray();
    }

    /**
     * Get departments.
     *
     * @return array
     */
    public function getDepartments(): array
    {
        return $this->departments->toArray();
    }

    public function getDepartment(DepartmentId $id): Department
    {
        $department = $this->departments->filter(function (Department $department) use ($id) {
            return $department->getId()->isEqual($id);
        })->first();

        if (empty($department)) {
            throw new DomainException('Department is not found.');
        }

        return $department;
    }

    public function getMembership(MemberId $id): Membership
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($id)) {
                return $membership;
            }
        }
        throw new \DomainException('Member is not found.');
    }
}