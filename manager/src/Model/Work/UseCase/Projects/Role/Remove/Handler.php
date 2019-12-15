<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Entity\Projects\Role\Id;
use DomainException;

/**
 * Class Handler.
 */
class Handler
{
    /**
     * @var RoleRepository $roles Role Repository.
     */
    private $roles;

    /**
     * @var Flusher $flusher Flusher.
     */
    private $flusher;
    /**
     * @var ProjectRepository
     */
    private $projects;

    /**
     * Handler constructor.
     *
     * @param RoleRepository    $roles   Role Repository.
     * @param Flusher           $flusher Flusher.
     * @param ProjectRepository $projects
     */
    public function __construct(RoleRepository $roles, Flusher $flusher, ProjectRepository $projects)
    {
        $this->roles = $roles;
        $this->flusher = $flusher;
        $this->projects = $projects;
    }

    /**
     * @param Command $command Command.
     */
    public function handle(Command $command): void
    {
        $role = $this->roles->get(new Id($command->id));

        if ($this->projects->hasMembersWithRole($role->getId())) {
            throw new DomainException('Role contains members.');
        }

        $this->roles->remove($role);
        $this->flusher->flush();
    }
}