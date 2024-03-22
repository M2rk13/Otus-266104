<?php

declare(strict_types=1);

namespace App\ProjectCascade\DBMaintence;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\TransactionIsolationLevel;


class DBTransactionService
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function beginTransaction($transactionIsolationLevel = TransactionIsolationLevel::REPEATABLE_READ): void
    {
        if (
            $this->connection->isTransactionActive() === false
            && $transactionIsolationLevel !== $this->connection->getTransactionIsolation()
        ) {
            $this->connection->setTransactionIsolation($transactionIsolationLevel);
        }

        $this->connection->beginTransaction();
    }

    /**
     * @throws Exception
     */
    public function commit($transactionIsolation = TransactionIsolationLevel::REPEATABLE_READ): void
    {
        $this->connection->commit();

        if (
            $this->connection->getTransactionNestingLevel() === 0
            && $this->connection->getTransactionIsolation() !== TransactionIsolationLevel::REPEATABLE_READ
        ) {
            $this->connection->setTransactionIsolation($transactionIsolation);
        }
    }

    /**
     * @throws Exception
     */
    public function rollback($transactionIsolation = TransactionIsolationLevel::REPEATABLE_READ): void
    {
        $this->connection->rollBack();

        if (
            $this->connection->getTransactionNestingLevel() === 0
            && $this->connection->getTransactionIsolation() !== TransactionIsolationLevel::REPEATABLE_READ
        ) {
            $this->connection->setTransactionIsolation($transactionIsolation);
        }
    }
}
