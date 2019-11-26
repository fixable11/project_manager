<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\ConfirmTokenSenderInterface;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Unit\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $users;

    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @var ConfirmTokenizer
     */
    private $tokenizer;
    /**
     * @var ConfirmTokenSenderInterface
     */
    private $sender;

    /**
     * @var Flusher
     */
    private $flusher;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        ConfirmTokenizer $tokenizer,
        ConfirmTokenSenderInterface $sender,
        Flusher $flusher
    ) {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);
        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}