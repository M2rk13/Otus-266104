<?php

namespace App\ProjectCascade\Service;

use App\Exception\ProjectException;
use App\Maintenance\Ioc\IoC;
use App\ProjectCascade\DBMaintence\DBTransactionService;
use App\ProjectCascade\Enum\SystemVariablesEnum;
use App\ProjectCascade\DBMaintence\Connection;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class IoCResolverService
{
    public static function getDBConnection(): Connection
    {
        $ioC = $GLOBALS['IoC'];

        try {
            return $ioC->resolve(SystemVariablesEnum::DATABASE_CONNECTION);
        } catch (ProjectException) {
            $ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::DATABASE_CONNECTION, function () {
                $DBConnectionParams = [
                    'dbname' => 'test',
                    'user' => 'root',
                    'password' => 'password',
                    'host' => $GLOBALS['mysql_host'],
                    'driver' => 'pdo_mysql',
                ];

                return new Connection($DBConnectionParams);
            });
        }

        return $ioC->resolve(SystemVariablesEnum::DATABASE_CONNECTION);
    }

    public static function getTransactionService(): DBTransactionService
    {
        $ioC = $GLOBALS['IoC'];

        try {
            return $ioC->resolve(SystemVariablesEnum::SQL_TRANSACTION_SERVICE);
        } catch (ProjectException) {
            $ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::SQL_TRANSACTION_SERVICE, function () {
                $connection = self::getDBConnection();

                return new DBTransactionService($connection);
            });
        }

        return $ioC->resolve(SystemVariablesEnum::SQL_TRANSACTION_SERVICE);
    }

    /**
     * @throws Exception
     */
    public static function getRabbitConnection(): AMQPStreamConnection
    {
        $ioC = $GLOBALS['IoC'];

        try {
            return $ioC->resolve(SystemVariablesEnum::RABBIT_CONNECTION);
        } catch (ProjectException) {
            $ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::RABBIT_CONNECTION, function () {
                return new AMQPStreamConnection($GLOBALS['rabbit_host'], 5672, 'rmuser', 'rmpassword');
            });
        }

        return $ioC->resolve(SystemVariablesEnum::RABBIT_CONNECTION);
    }
}
