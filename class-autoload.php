<?php

require_once __DIR__ . '/src/ProjectCascade/Billing/PaymentSystem/BillingBundle/PaymentSystemProviderInterface.php';
require_once __DIR__ . '/src/Maintenance/Interpreter/InterpretCommandInterface.php';
require_once __DIR__ . '/src/ProjectCascade/GateWay/HandlerRegistry/GateWayHandlerInterface.php';
require_once __DIR__ . '/src/Exception/ProjectException.php';
require_once __DIR__ . '/src/Exception/MathematicalException.php';
require_once __DIR__ . '/src/Exception/SpaceShipGameException.php';
require_once __DIR__ . '/src/ProjectCascade/Exception/ProjectCascadeException.php';
require_once __DIR__ . '/vendor/autoload.php';

$autoload = static function (string $pattern) {
    $classes = glob(__DIR__ . $pattern);

    foreach ($classes as $class) {
        require_once $class;
    }
};

$autoload('/src/*/*Interface.php');
$autoload('/src/*/*.php');
$autoload('/src/*/*/*Interface.php');
$autoload('/src/*/*/*.php');
$autoload('/src/ProjectCascade/Billing/PaymentSystem/*/*.php');
$autoload('/src/ProjectCascade/GateWay/UseCase/*/*.php');

use App\Maintenance\Ioc\IoC;
use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Billing\Registry\ProviderRegistry;
use App\ProjectCascade\DBMaintence\Connection;
use App\ProjectCascade\DBMaintence\DBTransactionService;
use App\ProjectCascade\Enum\SystemVariablesEnum;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerInterface;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerRegistry;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeConsumer;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeHandler;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeManager;
use App\ProjectCascade\RabbitMQ\RabbitClient;
use App\ProjectCascade\RabbitMQ\RabbitClientInterface;
use App\ProjectCascade\Service\BillingService;
use App\ProjectCascade\Service\BillingServiceManager;
use App\ProjectCascade\Service\QueueService;
use App\ProjectCascade\Service\QueueServiceInterface;
use App\ProjectCascade\UseCase\AuthHandler\AuthHandler;
use App\ProjectCascade\UseCase\AuthHandler\AuthInterface;
use App\ProjectCascade\UseCase\AuthHandler\AuthManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$isDocker = (bool) shell_exec('printenv IS_DOCKER');

$GLOBALS['rabbit_host'] = '127.0.0.1';
$GLOBALS['mysql_host'] = '127.0.0.1';

if ($isDocker) {
    $GLOBALS['rabbit_host'] = 'rabbitmq.proj';
    $GLOBALS['mysql_host'] = 'mysql.proj';
}

$classList = get_declared_classes();
$providerList = [];
$gatewayList = [];

foreach ($classList as $class) {
    if (in_array(
        PaymentSystemProviderInterface::class,
        class_implements($class),
        true
    )) {
        /** @var PaymentSystemProviderInterface $class */
        $providerList[$class::getName()] = $class;
    }

    if (in_array(
        GateWayHandlerInterface::class,
        class_implements($class),
        true
    )) {
        /** @var GateWayHandlerInterface $class */
        $gatewayList[$class::getUri()][$class::getMethod()] = $class;
    }
}

$GLOBALS['provider_list'] = $providerList;
$GLOBALS['gateway'] = $gatewayList;

$ioC = $GLOBALS['IoC'] = new IoC();

$ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::DATABASE_CONNECTION, function () use ($ioC) {
    $DBConnectionParams = [
        'dbname' => 'test',
        'user' => 'root',
        'password' => 'password',
        'host' => $GLOBALS['mysql_host'],
        'driver' => 'pdo_mysql',
    ];

    $dbConnection = new Connection($DBConnectionParams);

    $ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::DATABASE_CONNECTION, function () use ($dbConnection) {
        return $dbConnection;
    });

    return $dbConnection;
});

$ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::SQL_TRANSACTION_SERVICE, function () {
    $connection = $GLOBALS['IoC']->resolve(SystemVariablesEnum::DATABASE_CONNECTION);

    return new DBTransactionService($connection);
});

$ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::RABBIT_CONNECTION, function () use ($ioC) {
    $rabbitConnection = new AMQPStreamConnection($GLOBALS['rabbit_host'], 5672, 'rmuser', 'rmpassword');

    $ioC->resolve(IoC::IOC_REGISTER, SystemVariablesEnum::RABBIT_CONNECTION, function () use ($rabbitConnection) {
        return $rabbitConnection;
    });

    return $rabbitConnection;
});

$ioCPrepareClassFunc = static function ($key, $class) {
    return sprintf('$ioC->resolve(\App\Maintenance\Ioc\IoC::IOC_REGISTER, \'%s\', function () use ($ioC) {
        $class = new %s();

        $ioC->resolve(\App\Maintenance\Ioc\IoC::IOC_REGISTER, \'%s\', function () use ($class) {
            return $class;
        });

        return $class;
    });', $key, $class, $key);
};

eval($ioCPrepareClassFunc(RabbitClientInterface::class, RabbitClient::class));
eval($ioCPrepareClassFunc(ProviderRegistry::class, ProviderRegistry::class));
eval($ioCPrepareClassFunc(BillingService::class . 'DBManager', BillingServiceManager::class));
eval($ioCPrepareClassFunc(BillingService::class, BillingService::class));
eval($ioCPrepareClassFunc(CascadeHandler::class . 'DBManager', CascadeManager::class));
eval($ioCPrepareClassFunc(CascadeHandler::class, CascadeHandler::class));
eval($ioCPrepareClassFunc(CascadeConsumer::class, CascadeConsumer::class));
eval($ioCPrepareClassFunc(AuthHandler::class . 'DBManager', AuthManager::class));
eval($ioCPrepareClassFunc(AuthInterface::class, AuthHandler::class));
eval($ioCPrepareClassFunc(GateWayHandlerRegistry::class, GateWayHandlerRegistry::class));
eval($ioCPrepareClassFunc(QueueServiceInterface::class, QueueService::class));
