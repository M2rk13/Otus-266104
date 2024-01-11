<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Command\ConsumerRunCommand;
use App\Command\QueueHardStopCommand;
use App\Command\QueueSoftStopCommand;
use App\Queue\Consumer;
use App\Queue\ConsumerInterface;
use App\Queue\Queue;
use App\Tests\Helpers\EmptyTestCommand;
use PHPUnit\Framework\TestCase;

class ConsumerQueueTest extends TestCase
{
    public function testQueueWorks(): void
    {
        $queue = $this->getPreparedQueue();

        $consumer = new Consumer($queue);
        $consumer->setQueue($queue);
        $processor = new ConsumerRunCommand($consumer);

        $function = static function (ConsumerInterface $consumer) {
            $queue = $consumer->getQueue();

            if ($consumer->getProcessedTaskCounter() === 3) {
                $consumer->stopPropagation();

                return;
            }

            $command = $queue->extract();

            $command->execute();
            $consumer->incrementProcessedTasksCounter();
        };

        $processor->setProcessFunction($function);
        $processor->execute();

        self::assertGreaterThan(0, $consumer->getProcessedTaskCounter());
    }

    public function testHardStop(): void
    {
        $queue = $this->getPreparedQueue();

        $consumer = new Consumer($queue);
        $consumer->setQueue($queue);
        $processor = new ConsumerRunCommand($consumer);

        $queue->push(new QueueHardStopCommand($processor));
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        $processor->execute();

        $this->assertEquals(4, $consumer->getProcessedTaskCounter());

        $queue->push(new EmptyTestCommand());
        $processor->execute();

        $this->assertEquals(4, $consumer->getProcessedTaskCounter());
    }

    public function testSoftStop(): void
    {
        $queue = $this->getPreparedQueue();

        $consumer = new Consumer($queue);
        $consumer->setQueue($queue);
        $processor = new ConsumerRunCommand($consumer);

        $queue->push(new QueueSoftStopCommand($processor));
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());
        $queue->push(new EmptyTestCommand());

        $processor->execute();

        $this->assertEquals(7, $consumer->getProcessedTaskCounter());

        $queue->push(new EmptyTestCommand());
        $processor->execute();

        $this->assertEquals(7, $consumer->getProcessedTaskCounter());
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
