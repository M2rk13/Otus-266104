<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use App\Maintenance\Ioc\IoC;
use App\ProjectCascade\DBMaintence\Connection;
use App\ProjectCascade\DBMaintence\DBManager;
use App\ProjectCascade\DBMaintence\DBTransactionServiceInterface;
use App\ProjectCascade\Enum\SystemVariablesEnum;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class IoCResolverService
{
    public static function getDBConnection(): Connection
    {
        return self::getIoC()->resolve(SystemVariablesEnum::DATABASE_CONNECTION);
    }

    public static function getTransactionService(): DBTransactionServiceInterface
    {
        return self::getIoC()->resolve(SystemVariablesEnum::SQL_TRANSACTION_SERVICE);
    }

    public static function getRabbitConnection(): AMQPStreamConnection
    {
        return self::getIoC()->resolve(SystemVariablesEnum::RABBIT_CONNECTION);
    }

    public static function getManager(string $name): DBManager
    {
        return self::getIoC()->resolve($name . 'DBManager');
    }

    public static function getClass($name): object
    {
        return self::getIoC()->resolve($name);
    }

    private static function getIoC(): IoC
    {
        return $GLOBALS['IoC'];
    }
}
