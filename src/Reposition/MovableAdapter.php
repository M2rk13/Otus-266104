<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\SpaceObjectPropertyEnum;
use App\SpaceObjects\SpaceObjectInterface;

class MovableAdapter implements MovableInterface
{
    public function __construct(
        protected readonly SpaceObjectInterface $object,
    ) {
    }

    public function getPosition(): Coordinates
    {
        return $this->object->getProperty(SpaceObjectPropertyEnum::POSITION);
    }

    public function getVelocity(): Coordinates
    {
        return $this->object->getProperty(SpaceObjectPropertyEnum::VELOCITY);
    }

    public function setPosition(Coordinates $newVector): void
    {
        $this->object->setProperty(SpaceObjectPropertyEnum::POSITION, $newVector);
    }
}
