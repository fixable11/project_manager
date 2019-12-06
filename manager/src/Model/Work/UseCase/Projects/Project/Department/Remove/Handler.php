<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

/**
 * Class Handler.
 */
class Handler
{
    /**
     * @var ProjectRepository $projects Project.
     */
    private $projects;

    /**
     * @var Flusher $flusher Flusher.
     */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param ProjectRepository $projects Projects.
     * @param Flusher           $flusher  Flusher.
     */
    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $project = $this->projects->get(new Id($command->project));
        $project->removeDepartment(new DepartmentId($command->id));
        $this->flusher->flush();
    }
}