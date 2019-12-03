<?php

declare(strict_types=1);

namespace App\DataFixtures\Work\Members;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class GroupFixture extends Fixture
{
    /**
     * @var string REFERENCE_STAFF REFERENCE_STAFF.
     */
    public const REFERENCE_STAFF = 'work_member_group_staff';
    public const REFERENCE_CUSTOMERS = 'work_member_group_customers';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $staff = new Group(
            Id::next(),
            'Our Staff'
        );
        $manager->persist($staff);
        $this->setReference(self::REFERENCE_STAFF, $staff);

        $customers = new Group(
            Id::next(),
            'Customers'
        );

        $manager->persist($customers);
        $this->setReference(self::REFERENCE_CUSTOMERS, $customers);

        $manager->flush();
    }
}