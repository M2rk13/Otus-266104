<?php

declare(strict_types=1);

namespace App\Reposition;

use App\Enum\SpaceObjectPropertyEnum;
use App\SpaceObjects\SpaceObjectInterface;

class FuelBurnableAdapter implements FuelBurnableInterface
{
    public function __construct(
        private readonly SpaceObjectInterface $object,
    ) {
    }

    public function getFuelLevel(): int
    {
        return $this->object->getProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY);
    }

    public function setFuelLevel(int $fuelLevel): void
    {
        $this->object->setProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY, $fuelLevel);
    }

    public function getFuelConsumption(): int
    {
        return $this->object->getProperty(SpaceObjectPropertyEnum::FUEL_CONSUMPTION_PROPERTY);
    }
}
