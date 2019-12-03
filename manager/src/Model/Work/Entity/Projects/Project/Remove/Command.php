<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Remove;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command.
 */
class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * Command constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}