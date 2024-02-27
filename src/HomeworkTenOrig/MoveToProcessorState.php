<?php

declare(strict_types=1);

namespace App\HomeworkTenOrig;

use App\Queue\Queue;

class MoveToProcessorState extends AbstractProcessorState
{
    public function __construct(
        private readonly Queue $newQueue,
    ) {}

    public function __destruct()
    {
        $file = fopen(sys_get_temp_dir() . '/analog.txt', 'wb');
        fwrite($file, 'state changed to MoveToState');
        fclose($file);
    }

    public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface
    {
        $moveToState = new MoveToState($this->newQueue);
        $this->changeState($processor, $moveToState);

        return $moveToState;
    }
}
