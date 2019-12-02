<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User.
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_WAIT = 'wait';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @var Id $id Entity id.
     *
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var DateTimeImmutable $date Date of user creation.
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @var Email|null $email Email.
     *
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private $email;

    /**
     * @var string|null The hashed password
     *
     * @ORM\Column(type="string", name="password_hash", nullable=true)
     */
    private $passwordHash;

    /**
     * @var string|null $confirmToken User's token.
     *
     * @ORM\Column(type="string", name="confirm_token", nullable=true)
     */
    private $confirmToken;

    /**
     * @var ResetToken|null $resetToken Token for resetting user's password.
     *
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    /**
     * @var string $status User's status.
     *
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @var Network[]|ArrayCollection $networks Collection of networks.
     *
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $networks;

    /**
     * @var Role $role User's role.
     *
     * @ORM\Column(type="user_user_role", length=16)
     */
    private $role;

    /**
     * @var Email|null
     *
     * @ORM\Column(type="user_user_email", name="new_email", nullable=true)
     */
    private $newEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", name="new_email_token", nullable=true)
     */
    private $newEmailToken;

    /**
     * @var Name
     * @ORM\Embedded(class="Name")
     */
    private $name;

    /**
     * User constructor.
     *
     * @param Id                $id   Id vo.
     * @param DateTimeImmutable $date Date of user creation.
     * @param Name              $name User's name vo.
     */
    private function __construct(Id $id, DateTimeImmutable $date, Name $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->name = $name;
        $this->networks = new ArrayCollection();
        $this->role = Role::user();
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

    public function requestEmailChanging(Email $email, string $token): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if ($this->email && $this->email->isEqual($email)) {
            throw new \DomainException('Email is already same.');
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token): void
    {
        if (!$this->newEmailToken) {
            throw new \DomainException('Changing is not requested.');
        }
        if ($this->newEmailToken !== $token) {
            throw new \DomainException('Incorrect changing token.');
        }
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function edit(Email $email, Name $name): void
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public static function create(Id $id, DateTimeImmutable $date, Name $name, Email $email, string $hash): self
    {
        $user = new self($id, $date, $name);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    /**
     * Register by email.
     *
     * @param Id                $id    User's id.
     * @param DateTimeImmutable $date  Date of user creation.
     * @param Email             $email Email vo.
     * @param Name              $name  User's name vo.
     * @param string            $hash  Password hash.
     * @param string            $token Token for registering.
     *
     * @return User
     */
    public static function signUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Name $name,
        string $hash,
        string $token
    ): self
    {
        $user = new self($id, $date, $name);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('User is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }


    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new \DomainException('User is already blocked.');
        }
        $this->status = self::STATUS_BLOCKED;
    }

    /**
     * Register by social network.
     *
     * @param Id                $id       User's id.
     * @param DateTimeImmutable $date     Date of user creation.
     * @param Name              $name     Email vo.
     * @param string            $network  Name of network.
     * @param string            $identity Network identity.
     *
     * @return User
     *
     * @throws Exception
     */
    public static function signUpByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Name $name,
        string $network,
        string $identity
    ): self
    {
        $user = new self($id, $date, $name);
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
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status =self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    /**
     * Change user's name.
     *
     * @param Name $name User's name vo.
     */
    public function changeName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * Get user's name vo.
     *
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
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

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }
    
    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * @param string $network
     * @param string $identity
     *
     * @throws Exception
     */
    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    public function detachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isFor($network, $identity)) {
                if (!$this->email && $this->networks->count() === 1) {
                    throw new \DomainException('Unable to detach the last identity.');
                }
                $this->networks->removeElement($existing);
                return;
            }
        }
        throw new \DomainException('Network is not attached.');
    }
}