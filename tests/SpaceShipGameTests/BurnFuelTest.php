<?php

declare(strict_types=1);

namespace App\Tests\SpaceShipGameTests;

use App\Command\BurnFuelCommand;
use App\Command\CheckFuelCommand;
use App\Command\MoveWithFuelCommand;
use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\NotEnoughFuelException;
use App\Exception\PropertyNotFoundException;
use App\Reposition\Coordinates;
use App\Reposition\FuelBurnableAdapter;
use App\Reposition\MovableAdapter;
use App\SpaceObjects\Ship\DefaultShip;
use App\Tests\SweetsThief;
use PHPUnit\Framework\TestCase;

class BurnFuelTest extends TestCase
{
    /**
     * @throws NotEnoughFuelException
     */
    public function testCheckFuel(): void
    {
        $fuel = 100;
        $fuelStep = 100;
        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY, $fuel);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_CONSUMPTION_PROPERTY, $fuelStep);

        $burnFuelAdapter = new FuelBurnableAdapter($ship);
        $checkFuel = new CheckFuelCommand($burnFuelAdapter);

        $checkFuel->execute();

        self::assertEquals(1, 1);
    }

    /**
     * @throws NotEnoughFuelException
     */
    public function testCheckFuelException(): void
    {
        $fuel = 10;
        $fuelStep = 100;
        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY, $fuel);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_CONSUMPTION_PROPERTY, $fuelStep);

        $burnFuelAdapter = new FuelBurnableAdapter($ship);
        $checkFuel = new CheckFuelCommand($burnFuelAdapter);

        $this->expectException(NotEnoughFuelException::class);
        $checkFuel->execute();
    }

    /**
     * @throws NotEnoughFuelException
     * @throws PropertyNotFoundException
     */
    public function testBurnFuel(): void
    {
        $fuel = 100;
        $fuelStep = 10;
        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY, $fuel);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_CONSUMPTION_PROPERTY, $fuelStep);

        $burnFuelAdapter = new FuelBurnableAdapter($ship);
        $checkFuel = new CheckFuelCommand($burnFuelAdapter);

        $checkFuel->execute();

        $burnFuel = new BurnFuelCommand($burnFuelAdapter);
        $burnFuel->execute();

        $fuelValueResult = $ship->getProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY);

        self::assertEquals($fuel - $fuelStep, $fuelValueResult);
    }

    /**
     * @throws PropertyNotFoundException
     */
    public function testMoveBurnFuel(): void
    {
        $fuel = 100;
        $fuelStep = 5;
        $position = new Coordinates(1,1);
        $velocity = new Coordinates(2,-2);

        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::POSITION, $position);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY, $fuel);
        $ship->setProperty(SpaceObjectPropertyEnum::FUEL_CONSUMPTION_PROPERTY, $fuelStep);
        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);

        $fuelAdapter = new FuelBurnableAdapter($ship);
        $movableAdapter = new MovableAdapter($ship);

        $command = new MoveWithFuelCommand($fuelAdapter, $movableAdapter);
        $command->execute();

        $newFuelLevel = $ship->getProperty(SpaceObjectPropertyEnum::FUEL_LEVEL_PROPERTY);
        $newCoordinates = $ship->getProperty(SpaceObjectPropertyEnum::POSITION);
        $coordinatesThief = new SweetsThief($newCoordinates);

        self::assertEquals($fuel- $fuelStep, $newFuelLevel);
        self::assertEquals(3, $coordinatesThief->steeleSweets('x'));
        self::assertEquals(-1, $coordinatesThief->steeleSweets('y'));
    }
}
