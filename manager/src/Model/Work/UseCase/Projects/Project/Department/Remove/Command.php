<?php
declare(strict_types=1);
namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;
use Symfony\Component\Validator\Constraints as Assert;
class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $project;

    /**
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * Command constructor.
     *
     * @param string $project Project id.
     * @param string $id      Department id.
     */
    public function __construct(string $project, string $id)
    {
        $this->project = $project;
        $this->id = $id;
    }
}