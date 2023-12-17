<?php

declare(strict_types=1);

namespace App\Tests\SpaceShipGameTests;

use App\Command\MoveCommand;
use App\Enum\RepositionPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Reposition\Coordinates;
use App\Reposition\MovableAdapter;
use App\SpaceObjects\Ship\DefaultShip;
use App\Tests\SweetsThief;
use PHPUnit\Framework\TestCase;

use function sprintf;

class RepositionMovableTest extends TestCase
{
    /**
     * @dataProvider linearMoveDataProvider
     *
     * @throws PropertyNotFoundException
     */
    public function testLinearMove(array $coordinates, array $vector, array $expectedValue): void
    {
        $ship = new DefaultShip();

        $position = new Coordinates(...$coordinates);
        $ship->setProperty(RepositionPropertyEnum::POSITION, $position);

        $velocity = new Coordinates(...$vector);
        $ship->setProperty(RepositionPropertyEnum::VELOCITY, $velocity);

        $movableShip = new MovableAdapter($ship);

        $moveCommand = new MoveCommand($movableShip);
        $moveCommand();

        /** @var Coordinates $newCoordinates */
        $newCoordinates = $ship->getProperty(RepositionPropertyEnum::POSITION);

        $coordinatesThief = new SweetsThief($newCoordinates);

        self::assertEquals(
            $coordinatesThief->steeleSweets('x'),
            $expectedValue['x'],
            sprintf(
                'value not equal: got [%s], expected [%s]',
                $coordinatesThief->steeleSweets('x'),
                $expectedValue['x']
            )
        );
        self::assertEquals(
            $coordinatesThief->steeleSweets('y'),
            $expectedValue['y'],
            sprintf(
                'value not equal: got [%s], expected [%s]',
                $coordinatesThief->steeleSweets('y'),
                $expectedValue['y']
            )
        );
    }

    public static function linearMoveDataProvider(): array
    {
        return [
            'correctLinearMove' => [
                'coordinates' => [
                    'x' => 12,
                    'y' => 5,
                ],
                'vector' => [
                    'x' => -7,
                    'y' => 3,
                ],
                'expectedValue' => [
                    'x' => 5,
                    'y' => 8,
                ],
            ],
        ];
    }

    /**
     * @dataProvider moveErrorsProvider
     *
     * @throws PropertyNotFoundException
     */
    public function testMoveErrors(
        string $spaceObject,
        array $coordinates,
        array $vector,
        string $expectedException
    ): void
    {
        $this->expectException($expectedException);
        $object = new $spaceObject();

        if (empty($coordinates) === false) {
            $position = new Coordinates(...$coordinates);
            $object->setProperty(RepositionPropertyEnum::POSITION, $position);
        }

        if (empty($vector) === false) {
            $velocity = new Coordinates(...$vector);
            $object->setProperty(RepositionPropertyEnum::VELOCITY, $velocity);
        }

        $movableShip = new MovableAdapter($object);
        $moveCommand = new MoveCommand($movableShip);
        $moveCommand();
    }

    public static function moveErrorsProvider(): array
    {
        return [
            'emptyPosition' => [
                'object' => DefaultShip::class,
                'coordinates' => [
                ],
                'vector' => [
                    'x' => 1,
                    'y' => 1,
                ],
                'expectedException' => PropertyNotFoundException::class,
            ],
            'emptySpeed' => [
                'object' => DefaultShip::class,
                'coordinates' => [
                    'x' => 1,
                    'y' => 1,
                ],
                'vector' => [
                ],
                'expectedException' => PropertyNotFoundException::class,
            ],
        ];
    }
}
