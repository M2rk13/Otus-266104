<?php

declare(strict_types=1);

namespace App\Reposition;

interface MovableInterface
{
    public function getPosition(): Coordinates;
    public function getVelocity(): Coordinates;
    public function setPosition(Coordinates $newVector): void;
}
