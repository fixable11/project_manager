<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Class User.
 */
class User
{
    private const STATUS_NEW = 'new';
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';

    /**
     * @var Id $id Entity id.
     */
    private $id;

    /**
     * @var DateTimeImmutable $date Date of user creation.
     */
    private $date;

    /**
     * @var Email|null $email Email.
     */
    private $email;

    /**
     * @var string|null The hashed password
     */
    private $passwordHash;

    /**
     * @var string|null $confirmToken User's token.
     */
    private $confirmToken;

    /**
     * @var ResetToken|null $resetToken Token for resetting user's password.
     */
    private $resetToken;

    /**
     * @var string $status User's status.
     */
    private $status;

    /**
     * @var Network[]|ArrayCollection $networks Collection of networks.
     */
    private $networks;

    /**
     * User constructor.
     *
     * @param Id                $id   Id vo.
     * @param DateTimeImmutable $date Date of user creation.
     */
    private function __construct(Id $id, DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->networks = new ArrayCollection();
    }

    /**
     * Request for resetting password.
     *
     * @param ResetToken        $token Reset token.
     * @param DateTimeImmutable $date  Datetime.
     */
    public function requestPasswordReset(ResetToken $token, DateTimeImmutable $date): void
    {
        if (! $this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (! $this->email) {
            throw new \DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }
        $this->resetToken = $token;
    }

    /**
     * Method to reset password.
     *
     * @param DateTimeImmutable $date Datetime.
     * @param string            $hash Password hash.
     */
    public function passwordReset(DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $hash;
        $this->resetToken = null;
    }

    /**
     * Register by email.
     *
     * @param Id                $id    User's id.
     * @param DateTimeImmutable $date  Date of user creation.
     * @param Email             $email Email vo.
     * @param string            $hash  Password hash/
     * @param string            $token Token for registering.
     *
     * @return User
     */
    public static function signUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $hash,
        string $token
    ): self
    {
        $user = new self($id, $date);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    /**
     * Register by social network.
     *
     * @param Id                $id
     * @param DateTimeImmutable $date
     * @param string            $network  Name of network.
     * @param string            $identity Network identity.
     *
     * @return User
     *
     * @throws Exception
     */
    public static function signUpByNetwork(Id $id, \DateTimeImmutable $date, string $network, string $identity): self
    {
        $user = new self($id, $date);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    /**
     * Confirm registration.
     *
     * @return void
     */
    public function confirmSignUp(): void
    {
        $this->confirmToken = null;
    }

    /**
     * Checks if this is a new user
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Checks that user status equals to WAIT
     *
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     * Checks that user status equals to ACTIVE
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get user's id.
     *
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * Get user's email.
     *
     * @return null|Email
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * Gets password hash.
     *
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return null|string
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }
    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    /**
     * @param string $network
     * @param string $identity
     *
     * @throws Exception
     */
    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }
}