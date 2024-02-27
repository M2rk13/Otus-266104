<?php

declare(strict_types=1);

namespace App\HomeworkTenOrig;

interface ProcessorStateInterface
{
    public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface;

    public function changeState(ProcessorInterface $processor, ?ProcessorStateInterface $state): void;
}
