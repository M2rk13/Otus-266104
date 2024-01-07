<?php

declare(strict_types=1);

namespace App\Reposition;

interface VelocityChangeableInterface extends MovableInterface
{
    public function setVelocity(Coordinates $newVector): void;
}
