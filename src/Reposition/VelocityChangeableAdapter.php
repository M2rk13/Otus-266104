<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\SpaceObjectPropertyEnum;

class VelocityChangeableAdapter extends MovableAdapter implements VelocityChangeableInterface
{
    public function setVelocity(Coordinates $newVelocity): void
    {
        $this->object->setProperty(SpaceObjectPropertyEnum::VELOCITY, $newVelocity);
    }
}
