<?php

declare(strict_types=1);

namespace App\Enum;

enum SpaceObjectPropertyEnum
{
    public const VELOCITY = 'velocity';
    public const POSITION = 'position';
    public const ANGULAR_CHANGE = 'angularChange';
    public const ANGULAR_POSITION = 'angularPosition';
    public const FUEL_LEVEL_PROPERTY = 'fuelLevel';
    public const FUEL_CONSUMPTION_PROPERTY = 'fuelConsumption';
}
