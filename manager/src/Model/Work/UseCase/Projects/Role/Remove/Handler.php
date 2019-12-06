<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Entity\Projects\Role\Id;

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
     * @param RoleRepository $roles   Role Repository.
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
        $this->roles->remove($role);
        $this->flusher->flush();
    }
}