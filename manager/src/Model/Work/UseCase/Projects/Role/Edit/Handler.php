<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

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
     * Handler constructor.
     *
     * @param RoleRepository $roles   Roles.
     * @param Flusher        $flusher Flusher.
     */
    public function __construct(RoleRepository $roles, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command Command.
     */
    public function handle(Command $command): void
    {
        $role = $this->roles->get(new Id($command->id));
        $role->edit($command->name, $command->permissions);
        $this->flusher->flush();
    }
}