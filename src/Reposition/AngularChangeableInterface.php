<?php

declare(strict_types=1);

namespace App\Reposition;

interface AngularChangeableInterface extends RotatableInterface
{
    public function setAngularChange(float $newAngularChange): void;
}
