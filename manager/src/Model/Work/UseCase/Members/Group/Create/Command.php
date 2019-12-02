<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Group\Create;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command.
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;
}