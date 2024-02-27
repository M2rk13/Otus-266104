<?php

declare(strict_types=1);

namespace App\HomeworkTenOrig;

abstract class AbstractProcessorState implements ProcessorStateInterface
{
    abstract public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface;

    public function changeState(ProcessorInterface $processor, ?ProcessorStateInterface $state): void
    {
        $processor->changeState($state);
    }
}
