<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\SpaceObjectPropertyEnum;

class AngularChangeableAdapter extends RotatableAdapter implements AngularChangeableInterface
{
    public function setAngularChange(float $newAngularChange): void
    {
        $this->object->setProperty(SpaceObjectPropertyEnum::ANGULAR_CHANGE, $newAngularChange);
    }
}
