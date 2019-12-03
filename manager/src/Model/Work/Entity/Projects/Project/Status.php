<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;
use Webmozart\Assert\Assert;

class Status
{
    public const ACTIVE = 'active';
    public const ARCHIVED = 'archived';

    /**
     * @var string $name
     */
    private $name;

    /**
     * Status constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::ACTIVE,
            self::ARCHIVED,
        ]);
        $this->name = $name;
    }

    /**
     * @return static
     */
    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    /**
     * @return static
     */
    public static function archived(): self
    {
        return new self(self::ARCHIVED);
    }

    /**
     * @param Status $other
     *
     * @return bool
     */
    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->name === self::ACTIVE;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->name === self::ARCHIVED;
    }
}