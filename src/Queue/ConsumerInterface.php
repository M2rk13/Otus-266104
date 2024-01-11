<?php

declare(strict_types=1);

namespace App\Queue;

interface ConsumerInterface
{
    public function isPropagationStopped(): bool;

    public function stopPropagation(): void;

    public function getQueue(): Queue;

    public function setQueue(Queue $queue): void;

    public function process(callable $func);

    public function incrementProcessedTasksCounter(): void;

    public function getProcessedTaskCounter(): int;
}
