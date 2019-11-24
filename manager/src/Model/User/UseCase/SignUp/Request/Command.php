<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;


class Command
{
    /**
     * @var string $email Email.
     */
    public $email;

    /**
     * @var string $password Password.
     */
    public $password;
}