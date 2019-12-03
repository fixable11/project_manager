<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

/**
 * Class IdType.
 */
class IdType extends GuidType
{
    /**
     * @var string NAME
     */
    public const NAME = 'work_projects_project_id';

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return null|Id|mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Id($value) : null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}