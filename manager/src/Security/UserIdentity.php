<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserIdentity. Stores user's identity in session.
 */
class UserIdentity implements UserInterface, EquatableInterface
{
    /**
     * @var string $id User's id.
     */
    private $id;

    /**
     * @var string $username Username.
     */
    private $username;

    /**
     * @var string $password User's password.
     */
    private $password;

    /**
     * @var string $role User's role.
     */
    private $role;

    /**
     * @var string
     */
    private $status;

    /**
     * UserIdentity constructor.
     *
     * @param string $id       Id.
     * @param string $username Username.
     * @param string $password Password.
     * @param string $role     User's role.
     * @param string $status   User's status.
     */
    public function __construct(
        string $id,
        string $username,
        string $password,
        string $role,
        string $status
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
    }

    /**
     * Get user id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get user's roles.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof self) {
            return false;
        }
        return
            $this->id === $user->id &&
            $this->password === $user->password &&
            $this->role === $user->role &&
            $this->status === $user->status;
    }
}