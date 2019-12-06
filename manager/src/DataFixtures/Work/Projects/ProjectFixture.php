<?php

declare(strict_types=1);

namespace App\DataFixtures\Work\Projects;

use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;

class ProjectFixture extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $active = $this->createProject('First Project', 1);
        $manager->persist($active);

        $active->addDepartment(DepartmentId::next(), 'Development');
        $active->addDepartment(DepartmentId::next(), 'Marketing');

        $active = $this->createProject('Second Project', 2);
        $manager->persist($active);

        $archived = $this->createArchivedProject('Third Project', 3);
        $manager->persist($archived);

        $manager->flush();
    }

    /**
     * @param string $name
     * @param int    $sort
     *
     * @return Project
     *
     * @throws \Exception
     */
    private function createArchivedProject(string $name, int $sort): Project
    {
        $project = $this->createProject($name, $sort);
        $project->archive();

        return $project;
    }

    /**
     * @param string $name
     * @param int    $sort
     *
     * @return Project
     * @throws \Exception
     */
    private function createProject(string $name, int $sort): Project
    {
        return new Project(
            Id::next(),
            $name,
            $sort
        );
    }
}