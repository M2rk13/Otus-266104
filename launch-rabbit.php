<?php

namespace App;

require_once __DIR__ . '/src/Maintenance/Interpreter/InterpretCommandInterface.php';
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

use App\Maintenance\Ioc\IoC;
use App\ProjectCascade\Command\RabbitConsumerRunCommand;
use App\ProjectCascade\Enum\QueueEnum;

if (isset($GLOBALS['IoC']) === false) {
    $GLOBALS['IoC'] = new IoC();
}

$isDocker = (bool) shell_exec('printenv IS_DOCKER');

$GLOBALS['rabbit_host'] = '127.0.0.1';
$GLOBALS['mysql_host'] = '127.0.0.1';

if ($isDocker) {
    $GLOBALS['rabbit_host'] = 'rabbitmq.proj';
    $GLOBALS['mysql_host'] = 'mysql.proj';
}

$cmd = new RabbitConsumerRunCommand(QueueEnum::CASCADE_QUEUE);

$cmd->execute();
