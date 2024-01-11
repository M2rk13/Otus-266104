<?php

declare(strict_types=1);

namespace App\Command;

use App\Queue\ConsumerInterface;

class QueueHardStopCommand implements CommandInterface
{
    public function __construct(private readonly ConsumerRunCommand $processor)
    {
    }

    public function execute(): void
    {
        $hardStopFunction = static function (ConsumerInterface $consumer) {
            $consumer->stopPropagation();
        };

        $this->processor->setProcessFunction($hardStopFunction);
    }
}
