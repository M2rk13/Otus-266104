<?php

declare(strict_types=1);

namespace App\Maintenance\State;

use App\Command\CommandInterface;
use App\Queue\Queue;

class MoveToState extends AbstractProcessorState
{
    public function __construct(
        private readonly Queue $newQueue,
    ) {}

    public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface
    {
        $command = $processor->getQueue()->extract();

        if (is_null($command)) {
            $this->changeState($processor, null);

            return null;
        }

        $command instanceof ProcessorStateInterface ? $command->doNext($processor) : $this->sendToNewQueue($command);
        $processor->incrementExecutedCommandInMoveToState();

        return $this;
    }

    private function sendToNewQueue(CommandInterface $command) : void
    {
        $this->newQueue->push($command);
    }
}
