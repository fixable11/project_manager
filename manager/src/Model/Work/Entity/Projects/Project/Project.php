<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use Doctrine\Common\Collections\ArrayCollection;

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
            throw new \DomainException('Project is already archived.');
        }
        $this->status = Status::archived();
    }

    /**
     * Reinstate project.
     */
    public function reinstate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Project is already active.');
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
                throw new \DomainException('Department already exists.');
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

        throw new \DomainException('Department is not found.');
    }

    public function removeDepartment(DepartmentId $id): void
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                $this->departments->removeElement($department);
                return;
            }
        }

        throw new \DomainException('Department is not found.');
    }

    public function getDepartments()
    {
        return $this->departments->toArray();
    }

    public function getDepartment(DepartmentId $id): Department
    {
        $department = $this->departments->filter(function (Department $department) use ($id) {
            return $department->getId()->isEqual($id);
        })->first();

        if (empty($department)) {
            throw new \DomainException('Department is not found.');
        }

        return $department;
    }
}