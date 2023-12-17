<?php

declare(strict_types=1);

namespace App\Reposition;

interface RotatableInterface
{
    public function getAngular(): float;
    public function getAngularChange(): float;
    public function setAngular(float $angular): void;
}
