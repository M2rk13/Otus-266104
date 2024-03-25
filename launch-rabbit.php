<?php

namespace App;

require_once __DIR__ . '/class-autoload.php';

use App\ProjectCascade\Command\RabbitConsumerRunCommand;
use App\ProjectCascade\Enum\QueueEnum;

$cmd = new RabbitConsumerRunCommand(QueueEnum::CASCADE_QUEUE);

$cmd->execute();
