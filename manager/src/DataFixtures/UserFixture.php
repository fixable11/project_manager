<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('123123');

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name('James', 'Bond'),
            new Email('test@gmail.com'),
            $hash,
            'token'
        );

        $user->confirmSignUp();

        $user->changeRole(Role::admin());

        $manager->persist($user);

        $manager->flush();
    }
}