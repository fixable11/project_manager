<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use PDO;

/**
 * Class RoleFetcher
 *
 * @package App\ReadModel\Work\Projects
 */
class RoleFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * RoleFetcher constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'r.id',
                'r.name',
                'r.permissions'
            )
            ->from('work_projects_roles', 'r')
            ->orderBy('name')
            ->execute();

        return array_map(static function (array $role) {
            return array_replace($role, [
                'permissions' => json_decode($role['permissions'], true)
            ]);
        }, $stmt->fetchAll(FetchMode::ASSOCIATIVE));
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_roles')
            ->orderBy('name')
            ->execute();

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}