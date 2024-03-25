<?php

declare(strict_types=1);

namespace App\ProjectCascade\DBMaintence;

use App\ProjectCascade\Exception\DBException;
use App\ProjectCascade\Service\IdGenerator;
use DateTimeImmutable;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\ServerVersionProvider;
use Doctrine\DBAL\TransactionIsolationLevel;
use SensitiveParameter;

class Connection implements ServerVersionProvider
{
    private DbalConnection $dbalConnection;

    public function __construct(
        #[SensitiveParameter]
        array $params,
        ?Configuration $config = null,
    ) {
        $this->dbalConnection = DriverManager::getConnection(
            $params,
            $config,
        );
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function insert(string $table, array $data, array $types = []): string
    {
        if (isset($data['createdAt']) || isset($data['updatedAt'])) {
            throw new DBException('do not set system createdAt / updatedAt values');
        }

        $this->addId($data);
        $this->addCreatedAt($data);
        $this->addUpdatedAt($data);

        $this->dbalConnection->insert($table, $data, $types);

        return $data['id'];
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function update(string $table, array $data, array $criteria = [], array $types = []): int|string
    {
        if (isset($data['createdAt']) || isset($data['updatedAt'])) {
            throw new DBException('do not set system createdAt / updatedAt values');
        }

        $this->addUpdatedAt($data);

        return $this->dbalConnection->update($table, $data, $criteria, $types);
    }

    private function addId(array &$data): void
    {
        if (isset($data['id']) === false) {
            $data['id'] = IdGenerator::generateUniqueId();
        }
    }

    /**
     * @throws DBException
     */
    private function addCreatedAt(array &$data): void
    {
        if (isset($data['createdAt'])) {
            throw new DBException('do not set system createdAt value');
        }

        $data['createdAt'] = (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }

    /**
     * @throws DBException
     */
    private function addUpdatedAt(array &$data): void
    {
        if (isset($data['updatedAt'])) {
            throw new DBException('do not set system updatedAt value');
        }

        if (isset($data['createdAt'])) {
            $data['updatedAt'] = $data['createdAt'];

            return;
        }

        $data['updatedAt'] = (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->dbalConnection->createQueryBuilder();
    }

    /**
     * @throws Exception
     */
    public function executeQuery(
        string $sql,
        array $params = [],
        array $types = [],
        ?QueryCacheProfile $qcp = null,
    ): Result {
        return $this->dbalConnection->executeQuery($sql, $params, $types, $qcp);
    }

    public function isTransactionActive(): bool
    {
        return $this->dbalConnection->isTransactionActive();
    }

    /**
     * @throws Exception
     */
    public function getTransactionIsolation(): TransactionIsolationLevel
    {
        return $this->dbalConnection->getTransactionIsolation();
    }

    /**
     * @throws Exception
     */
    public function setTransactionIsolation(TransactionIsolationLevel $transactionIsolationLevel): void
    {
        $this->dbalConnection->setTransactionIsolation($transactionIsolationLevel);
    }

    /**
     * @throws Exception
     */
    public function beginTransaction(): void
    {
        $this->dbalConnection->beginTransaction();
    }

    /**
     * @throws Exception
     */
    public function commit(): void
    {
        $this->dbalConnection->commit();
    }

    public function getTransactionNestingLevel(): int
    {
        return $this->dbalConnection->getTransactionNestingLevel();
    }

    /**
     * @throws Exception
     */
    public function rollBack(): void
    {
        $this->dbalConnection->rollBack();
    }

    /**
     * @throws Exception
     */
    public function delete(string $table, array $criteria = [], array $types = []): int|string
    {
        return $this->dbalConnection->delete($table, $criteria, $types);
    }

    /**
     * @throws Exception
     */
    public function getServerVersion(): string
    {
        return $this->dbalConnection->getServerVersion();
    }
}
