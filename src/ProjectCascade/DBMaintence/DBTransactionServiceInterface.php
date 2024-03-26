<?php

declare(strict_types=1);

namespace App\ProjectCascade\DBMaintence;

use Doctrine\DBAL\TransactionIsolationLevel;

interface DBTransactionServiceInterface
{
    public function beginTransaction($transactionIsolationLevel = TransactionIsolationLevel::REPEATABLE_READ): void;
    public function commit($transactionIsolation = TransactionIsolationLevel::REPEATABLE_READ): void;
    public function rollback($transactionIsolation = TransactionIsolationLevel::REPEATABLE_READ): void;
}
