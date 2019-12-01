<?php

declare(strict_types=1);

namespace App\ReadModel\User;

/**
 * Class AuthView.
 */
class AuthView
{
    public $id;
    public $email;
    public $password_hash;
    public $name;
    public $role;
    public $status;
}