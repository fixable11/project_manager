<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command.
 */
class Command
{
    /**
     * @var string $email Email.
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string $password Password.
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $password;
}