<?php

declare(strict_types=1);

namespace App\HomeworkTenOrig;

use App\Queue\Queue;

interface ProcessorInterface
{
    public function run(): void;

    public function changeState(?ProcessorStateInterface $state): void;

    public function getQueue(): Queue;

    public function incrementExecutedCommandInRunState(): void;

    public function incrementExecutedCommandInMoveToState(): void;
}
