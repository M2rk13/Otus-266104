<?php

declare(strict_types=1);

namespace App\Maintenance\State;

class RunProcessorState extends AbstractProcessorState
{
    public function __destruct()
    {
        $file = fopen(sys_get_temp_dir() . '/analog.txt', 'wb');
        fwrite($file, 'state changed to RunState');
        fclose($file);
    }

    public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface
    {
        $runState = new RunState();
        $this->changeState($processor, $runState);

        return $runState;
    }
}
