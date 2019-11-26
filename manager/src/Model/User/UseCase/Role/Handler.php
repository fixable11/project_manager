<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;


use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\UserRepository;
use App\Tests\Unit\Model\Flusher;

class Handler
{
    /**
     * @var UserRepository $users Users.
     */
    private $users;

    /**
     * @var Flusher $flusher Flusher.
     */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepository $users   Users.
     * @param Flusher        $flusher Flusher,
     */
    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    /**
     * Handle changing user's password.
     *
     * @param Command $command Command for changing user's role.
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $user->changeRole(new Role($command->role));
        $this->flusher->flush();
    }
}