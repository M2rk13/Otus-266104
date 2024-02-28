<?php

declare(strict_types=1);

namespace App\Maintenance\State;

class StateHardStopProcessorState extends AbstractProcessorState
{
    public function __destruct()
    {
        $file = fopen(sys_get_temp_dir() . '/analog.txt', 'wb');
        fwrite($file, 'state changed to HardStopState');
        fclose($file);
    }

    public function doNext(ProcessorInterface $processor): ?ProcessorStateInterface
    {
        $this->changeState($processor, null);

        return null;
    }
}
