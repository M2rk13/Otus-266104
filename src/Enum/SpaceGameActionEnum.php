<?php

declare(strict_types=1);

namespace App\Enum;

enum SpaceGameActionEnum
{
    public const ACTION_START_MOVE = 'startMove';
    public const ACTION_STOP_MOVE = 'stopMove';
    public const ACTION_FIRE = 'actionFire';
}
