<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\RepositionPropertyEnum;
use App\SpaceObjects\SpaceObjectInterface;

class RotatableAdapter implements RotatableInterface
{
    private SpaceObjectInterface $object;

    public function __construct(
        SpaceObjectInterface $object,
    ) {
        $this->object = $object;
    }

    public function getAngular(): float
    {
        return $this->object->getProperty(RepositionPropertyEnum::ANGULAR_POSITION);
    }

    public function getAngularChange(): float
    {
        return $this->object->getProperty(RepositionPropertyEnum::ANGULAR_CHANGE);
    }

    public function setAngular(float $angular): void
    {
        $this->object->setProperty(RepositionPropertyEnum::ANGULAR_POSITION, $angular);
    }
}
