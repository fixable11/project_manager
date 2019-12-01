<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Block;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

/**
 * Class Handler.
 */
class Handler
{
    private $users;
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepository $users
     * @param Flusher        $flusher
     */
    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $user->block();
        $this->flusher->flush();
    }
}