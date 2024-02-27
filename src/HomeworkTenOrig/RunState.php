<?php

declare(strict_types=1);

namespace App\HomeworkTenOrig;

class RunState extends AbstractProcessorState
{
    public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface
    {
        $command = $processor->getQueue()->extract();

        if (is_null($command)) {
            $this->changeState($processor, null);

            return null;
        }

        $command instanceof ProcessorStateInterface ? $command->doNext($processor) : $command->execute();
        $processor->incrementExecutedCommandInRunState();

        return $this;
    }
}