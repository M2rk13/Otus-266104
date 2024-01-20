<?php

namespace App\Command;

use App\Queue\ConsumerInterface;

class QueueSoftStopCommand implements CommandInterface
{
    public function __construct(private readonly ConsumerRunCommand $processor)
    {
    }

    public function execute(): void
    {
        $softStopFunction = static function (ConsumerInterface $consumer) {
            $queue = $consumer->getQueue();

            if ($queue->count() === 0) {
                $consumer->stopPropagation();

                return;
            }

            $command = $queue->extract();
            $command->execute();

            $consumer->incrementProcessedTasksCounter();
        };

        $this->processor->setProcessFunction($softStopFunction);
    }
}
