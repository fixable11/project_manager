<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

/**
 * Class Command.
 */
class Command
{
    /**
     * @var string
     */
    public $token;
    /**
     * @var string
     */
    public $password;

    /**
     * Command constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }
}