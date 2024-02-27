<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\HomeworkTenOrig\MoveToProcessorState;
use App\HomeworkTenOrig\Processor;
use App\HomeworkTenOrig\RunProcessorState;
use App\HomeworkTenOrig\RunState;
use App\HomeworkTenOrig\StateHardStopProcessorState;
use App\Queue\Queue;
use App\Tests\Helpers\EmptyTestCommand;
use App\Tests\Helpers\SweetsThief;
use PHPUnit\Framework\TestCase;

class ProcessorStateTest extends TestCase
{
    public function testHardStopState(): void
    {
        $queue = $this->getPreparedQueue();

        $queue->push(new StateHardStopProcessorState());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        $processor = new Processor($queue, new RunState());
        $processor->run();

        $countThief = new SweetsThief($processor);
        $countExecutedCommandInRunState = $countThief->steeleSweets('countExecutedCommandInRunState');
        $logFile = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        $this->assertEquals(4, $countExecutedCommandInRunState);
        $this->assertEquals('state changed to HardStopState', $logFile);
    }

    public function testMoveToState(): void
    {
        $queue = $this->getPreparedQueue();
        $newQueue = new Queue();

        $queue->push(new MoveToProcessorState($newQueue));
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        $processor = new Processor($queue, new RunState());
        $processor->run();

        $countThief = new SweetsThief($processor);
        $countExecutedCommandInMoveToState = $countThief->steeleSweets('countExecutedCommandInMoveToState');
        $commandCountInNewQueue = $newQueue->count();
        $logFile = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        $this->assertEquals(3, $commandCountInNewQueue);
        $this->assertEquals($commandCountInNewQueue, $countExecutedCommandInMoveToState);
        $this->assertEquals('state changed to MoveToState', $logFile);
    }

    public function testRunStateAfterMoveToState(): void
    {
        $queue = $this->getPreparedQueue();
        $newQueue = new Queue();

        $queue->push(new MoveToProcessorState($newQueue));
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        $queue->push(new RunProcessorState());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        $processor = new Processor($queue, new RunState());
        $processor->run();

        $countThief = new SweetsThief($processor);
        $countExecutedCommandInRunState = $countThief->steeleSweets('countExecutedCommandInRunState');
        $logFile = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        $this->assertEquals(6, $countExecutedCommandInRunState);
        $this->assertEquals('state changed to RunState', $logFile);
    }

    private function getPreparedQueue(): Queue
    {
        $queue = new Queue();

        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        return $queue;
    }
}
