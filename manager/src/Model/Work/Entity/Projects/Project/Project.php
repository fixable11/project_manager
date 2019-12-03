<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\ORM\Mapping as ORM;

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
}