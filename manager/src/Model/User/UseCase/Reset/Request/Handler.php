<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSenderInterface;
use App\Tests\Unit\Model\Flusher;

/**
 * Class Handler.
 */
class Handler
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var ResetTokenizer
     */
    private $tokenizer;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ResetTokenSenderInterface $sender Sender.
     */
    private $sender;

    /**
     * Handler constructor.
     *
     * @param UserRepository            $users
     * @param ResetTokenizer            $tokenizer
     * @param Flusher                   $flusher
     * @param ResetTokenSenderInterface $sender
     */
    public function __construct(
        UserRepository $users,
        ResetTokenizer $tokenizer,
        Flusher $flusher,
        ResetTokenSenderInterface $sender
    ) {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));
        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );
        $this->flusher->flush();
        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}