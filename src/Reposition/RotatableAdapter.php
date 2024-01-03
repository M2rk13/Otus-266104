<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\SpaceObjectPropertyEnum;
use App\SpaceObjects\SpaceObjectInterface;

class RotatableAdapter implements RotatableInterface
{
    public function __construct(
        protected readonly SpaceObjectInterface $object,
    ) {
    }

    public function getAngular(): float
    {
        return $this->object->getProperty(SpaceObjectPropertyEnum::ANGULAR_POSITION);
    }

    public function getAngularChange(): float
    {
        return $this->object->getProperty(SpaceObjectPropertyEnum::ANGULAR_CHANGE);
    }

    public function setAngular(float $angular): void
    {
        $this->object->setProperty(SpaceObjectPropertyEnum::ANGULAR_POSITION, $angular);
    }
}
