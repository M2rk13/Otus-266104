<?php

declare(strict_types=1);

namespace App\ProjectCascade\Enum;

enum QueueEnum
{
    public const MAIN_EXCHANGE = 'main_exchange';
    public const CASCADE_QUEUE = 'cascade_queue';
}
