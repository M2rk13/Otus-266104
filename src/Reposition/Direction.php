<?php

declare(strict_types=1);

namespace App\Reposition;

use function deg2rad;
use function rad2deg;
use function round;

class Direction
{
    public readonly float $angular;

    public function __construct(float $angular)
    {
        $this->angular = $angular;
    }

    public static function plus(float $a1, float $a2): float
    {
        $aRad1 = deg2rad($a1);
        $aRad2 = deg2rad($a2);

        return round(rad2deg($aRad1 + $aRad2));
    }
}
