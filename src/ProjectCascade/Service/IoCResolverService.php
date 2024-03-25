<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use App\ProjectCascade\DBMaintence\Connection;
use App\ProjectCascade\DBMaintence\DBManager;
use App\ProjectCascade\DBMaintence\DBTransactionServiceInterface;
use App\ProjectCascade\Enum\SystemVariablesEnum;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class IoCResolverService
{
    public static function getDBConnection(): Connection
    {
        return $GLOBALS['IoC']->resolve(SystemVariablesEnum::DATABASE_CONNECTION);
    }

    public static function getTransactionService(): DBTransactionServiceInterface
    {
        return $GLOBALS['IoC']->resolve(SystemVariablesEnum::SQL_TRANSACTION_SERVICE);
    }

    public static function getRabbitConnection(): AMQPStreamConnection
    {
        return $GLOBALS['IoC']->resolve(SystemVariablesEnum::RABBIT_CONNECTION);
    }

    public static function getManager(string $name): DBManager
    {
        return $GLOBALS['IoC']->resolve($name . 'DBManager');
    }

    public static function getClass($name): object
    {
        return $GLOBALS['IoC']->resolve($name);
    }
}
