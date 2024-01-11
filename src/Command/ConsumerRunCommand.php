<?php

namespace App\Command;

use App\Queue\ConsumerInterface;
use Closure;

class ConsumerRunCommand implements CommandInterface
{
    private Closure $callable;

    public function __construct(private readonly ConsumerInterface $consumer)
    {
        $runFunction = static function (ConsumerInterface $consumer) {
            $queue = $consumer->getQueue();

            if ($queue->count() === 0) {
                return;
            }

            $command = $queue->extract();

            $command->execute();
            $consumer->incrementProcessedTasksCounter();
        };

        $this->setProcessFunction($runFunction);
    }

    public function execute(): void
    {
        while ($this->consumer->isPropagationStopped() === false) {
            $this->consumer->process($this->callable);
        }
    }

    public function setProcessFunction(Closure $callable): void
    {
        $this->callable = $callable;
    }
}
