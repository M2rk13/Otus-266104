<?php

declare(strict_types=1);

namespace App\Maintenance\Interpreter;

use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;

interface InterpretCommandInterface
{
    public const TAG = 'interpreter_command';
    public const IOC_KEY_FORMAT = '%s[.]%s';

    public function execute(IoC $ioC, InterpretObject $order, Queue $queue);
}
