<?php

declare(strict_types=1);

namespace App\ProjectCascade\DBMaintence;

use App\ProjectCascade\Service\IoCResolverService;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class DBManager
{
    protected Connection $connection;

    public function __construct()
    {
        $this->connection = IoCResolverService::getDBConnection();
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @throws Exception
     */
    public function fetchOne(QueryBuilder $queryBuilder): mixed
    {
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes()
        );

        return $stmt->fetchOne();
    }

    /**
     * @throws Exception
     */
    public function fetchAll(QueryBuilder $queryBuilder): array
    {
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes()
        );

        return $stmt->fetchAllAssociative();
    }
}
