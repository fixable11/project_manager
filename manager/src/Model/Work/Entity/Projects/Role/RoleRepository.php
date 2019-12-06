<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Role;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Class RoleRepository.
 */
class RoleRepository
{
    /**
     * @var EntityRepository
     */
    private $repo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RoleRepository constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Role::class);
        $this->em = $em;
    }

    /**
     * @param string $name
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByName(string $name): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.name)')
                ->andWhere('t.name = :name')
                ->setParameter(':name', $name)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Id $id
     *
     * @return Role
     */
    public function get(Id $id): Role
    {
        /** @var Role $role */
        if (!$role = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Role is not found.');
        }
        return $role;
    }

    /**
     * @param Role $role
     */
    public function add(Role $role): void
    {
        $this->em->persist($role);
    }

    /**
     * @param Role $role
     */
    public function remove(Role $role): void
    {
        $this->em->remove($role);
    }
}