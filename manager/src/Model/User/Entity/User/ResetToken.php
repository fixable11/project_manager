<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

/**
 * Class ResetToken.
 */
class ResetToken
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTimeImmutable
     */
    private $expires;

    /**
     * ResetToken constructor.
     *
     * @param string             $token
     * @param \DateTimeImmutable $expires
     */
    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        Assert::notEmpty($token);
        $this->token = $token;
        $this->expires = $expires;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}