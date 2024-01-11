<?php

declare(strict_types=1);

namespace App\Queue;

class Consumer implements ConsumerInterface
{
    private bool $propagationStopped = false;
    private int $taskCounter = 0;

    public function __construct(private Queue $queue)
    {
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function getQueue(): Queue
    {
        return $this->queue;
    }

    public function setQueue(Queue $queue): void
    {
        $this->queue = $queue;
    }

    public function process(callable $func): void
    {
        $func($this);
    }

    public function incrementProcessedTasksCounter(): void
    {
        $this->taskCounter++;
    }

    public function getProcessedTaskCounter(): int
    {
        return $this->taskCounter;
    }
}
