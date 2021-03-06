<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Exception;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Id.
 */
class Id
{
    /**
     * @var string $value Id value.
     */
    private $value;

    /**
     * Id constructor.
     *
     * @param string $value Id value.
     */
    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    /**
     * Generate next id.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}