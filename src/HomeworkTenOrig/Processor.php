<?php

declare(strict_types=1);

namespace App\HomeworkTenOrig;

use App\Queue\Queue;

class Processor implements ProcessorInterface
{
    private int $countExecutedCommandInRunState;
    private int $countExecutedCommandInMoveToState;

    public function __construct(
        private readonly Queue $queue,
        private ?ProcessorStateInterface $state,
    ) {
        $this->countExecutedCommandInRunState = 0;
        $this->countExecutedCommandInMoveToState = 0;
    }

    public function run(): void
    {
        do {
            $next = $this->state?->doNext($this);

        } while ($next !== null);
    }

    public function changeState(?ProcessorStateInterface $state): void
    {
        $this->state = $state;
    }

    public function getQueue(): Queue
    {
        return $this->queue;
    }

    public function incrementExecutedCommandInRunState(): void
    {
        $this->countExecutedCommandInRunState++;
    }

    public function incrementExecutedCommandInMoveToState(): void
    {
        $this->countExecutedCommandInMoveToState++;
    }
}
