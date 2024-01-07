<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\SpaceObjectPropertyEnum;

class VelocityChangeableAdapter extends MovableAdapter implements VelocityChangeableInterface
{
    public function setVelocity(Coordinates $newVector): void
    {
        $this->object->setProperty(SpaceObjectPropertyEnum::VELOCITY, $newVector);
    }
}
