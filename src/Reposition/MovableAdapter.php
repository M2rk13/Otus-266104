<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\RepositionPropertyEnum;
use App\SpaceObjects\SpaceObjectInterface;

class MovableAdapter implements MovableInterface
{
    private SpaceObjectInterface $object;

    public function __construct(
        SpaceObjectInterface $object,
    ) {
        $this->object = $object;
    }

    public function getPosition(): Coordinates
    {
        return $this->object->getProperty(RepositionPropertyEnum::POSITION);
    }

    public function getVelocity(): Coordinates
    {
        return $this->object->getProperty(RepositionPropertyEnum::VELOCITY);
    }

    public function setPosition(Coordinates $newVector): void
    {
        $this->object->setProperty(RepositionPropertyEnum::POSITION, $newVector);
    }
}
