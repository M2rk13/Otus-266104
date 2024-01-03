<?php

declare(strict_types=1);

namespace App\Tests\SpaceShipGameTests;

use App\Command\ChangeVelocityCommand;
use App\Command\RotateWithChangeVelocityCommand;
use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Reposition\Coordinates;
use App\Reposition\VelocityChangeableAdapter;
use App\SpaceObjects\Ship\DefaultShip;
use PHPUnit\Framework\TestCase;

class VelocityChangeTest extends TestCase
{
    /**
     * @throws PropertyNotFoundException
     */
    public function testBasicChangeVelocity(): void
    {
        $velocity = new Coordinates(1, 1);
        $changeVelocity = new Coordinates(3,2);
        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);
        $adapter = new VelocityChangeableAdapter($ship);

        $command = new ChangeVelocityCommand($adapter, $changeVelocity);
        $command->execute();

        $newVelocity = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);

        self::assertSame($changeVelocity, $newVelocity);
    }


    /**
     * @throws PropertyNotFoundException
     */
    public function testChangeVelocityByAngular(): void
    {
        $velocity = new Coordinates(x: 0, y: 5);
        $angular = 90;
        $angularChange = -90;
        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);
        $ship->setProperty(SpaceObjectPropertyEnum::ANGULAR_POSITION, $angular);
        $ship->setProperty(SpaceObjectPropertyEnum::ANGULAR_CHANGE, $angularChange);

        $command = new RotateWithChangeVelocityCommand($ship);
        $command->execute();

        $newVelocity = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $newAngular = $ship->getProperty(SpaceObjectPropertyEnum::ANGULAR_POSITION);

        self::assertEquals($angular + $angularChange, $newAngular);
        self::assertEquals(new Coordinates(x: 5, y: 0), $newVelocity);
    }
}
