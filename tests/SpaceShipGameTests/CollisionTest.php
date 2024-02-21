<?php

declare(strict_types=1);

namespace App\Tests\SpaceShipGameTests;

use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\CollisionCheckHandler\CheckCollisionCommand;
use App\Maintenance\Dto\GameFieldChunkDto;
use App\Maintenance\Dto\GameFieldChunkListDto;
use App\SpaceObjects\Ship\DefaultShip;
use PHPUnit\Framework\TestCase;

use function ksort;

class CollisionTest extends TestCase
{
    /**
     * @throws PropertyNotFoundException
     */
    public function testSuccessLaunch(): void
    {
        $gameFieldListDto = new GameFieldChunkListDto([]);

        $checkNearbyAndCollisionsCommand = new CheckCollisionCommand($gameFieldListDto);
        $checkNearbyAndCollisionsCommand->execute();

        $this->assertTrue(true);
    }

    /**
     * @dataProvider providerAvoidCollision
     *
     * @throws PropertyNotFoundException
     */
    public function testAvoidCollision($parameterList): void
    {
        $gameObjectList = [];
        $expectedResult = [];
        $counter = count($parameterList);

        foreach ($parameterList as $shipName => $shipPosition) {
            $gameObjectList[] = $this->prepareGameObject($shipName, $shipPosition);
            $expectedResult[$counter--] = sprintf('shipName: [%s], position: [%s]', $shipName, $shipPosition);
        }

        ksort($expectedResult);

        $gameField = new GameFieldChunkDto(1, $gameObjectList);
        $gameFieldList = new GameFieldChunkListDto([$gameField]);

        $checkNearbyAndCollisionsCommand = new CheckCollisionCommand($gameFieldList);
        $checkNearbyAndCollisionsCommand->execute();

        $actualResult = [];

        foreach ($gameFieldList->gameFieldListDto as $gameFieldDto) {
            foreach ($gameFieldDto->gameObjectList as $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);
                $name = $gameObject->getProperty(SpaceObjectPropertyEnum::SPACE_OBJECT_NAME);

                $actualResult[$gameFieldDto->id] = sprintf('shipName: [%s], position: [%s]', $name, $position);
            }
        }

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider providerNoCollision
     *
     * @throws PropertyNotFoundException
     */
    public function testNoCollision($parameterList): void
    {
        $gameObjectList = [];
        $expectedResult = [];

        foreach ($parameterList as $shipName => $shipPosition) {
            $gameObjectList[] = $this->prepareGameObject($shipName, $shipPosition);
            $expectedResult[1][] = sprintf('shipName: [%s], position: [%s]', $shipName, $shipPosition);
        }

        ksort($expectedResult);

        $gameField = new GameFieldChunkDto(1, $gameObjectList);
        $gameFieldList = new GameFieldChunkListDto([$gameField]);

        $checkNearbyAndCollisionsCommand = new CheckCollisionCommand($gameFieldList);
        $checkNearbyAndCollisionsCommand->execute();

        $actualResult = [];

        foreach ($gameFieldList->gameFieldListDto as $gameFieldDto) {
            foreach ($gameFieldDto->gameObjectList as $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);
                $name = $gameObject->getProperty(SpaceObjectPropertyEnum::SPACE_OBJECT_NAME);

                $actualResult[$gameFieldDto->id][] = sprintf('shipName: [%s], position: [%s]', $name, $position);
            }
        }

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider providerSeveralCollision
     *
     * @throws PropertyNotFoundException
     */
    public function testSeveralCollision($parameterList): void
    {
        $gameFieldList = [];

        foreach ($parameterList as $key => $field) {
            $gameObjectList = [];

            foreach ($field as $shipName => $shipPosition) {
                $gameObjectList[] = $this->prepareGameObject($shipName, $shipPosition);
            }

            $filedId = $key + 1;
            $gameFieldList[] = new GameFieldChunkDto($filedId, $gameObjectList);
        }

        $expectedResult = [
            1 => 'shipName: [Science ship], position: [1]',
            2 => 'shipName: [Transport ship], position: [1]',
            3 => 'shipName: [Colony ship], position: [1]',
            4 => 'shipName: [Construction ship], position: [1]',
        ];

        $gameFieldDtoList = new GameFieldChunkListDto($gameFieldList);

        $checkNearbyAndCollisionsCommand = new CheckCollisionCommand($gameFieldDtoList);
        $checkNearbyAndCollisionsCommand->execute();

        $actualResult = [];

        foreach ($gameFieldDtoList->gameFieldListDto as $gameFieldDto) {
            foreach ($gameFieldDto->gameObjectList as $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);
                $name = $gameObject->getProperty(SpaceObjectPropertyEnum::SPACE_OBJECT_NAME);

                $actualResult[$gameFieldDto->id] = sprintf('shipName: [%s], position: [%s]', $name, $position);
            }
        }

        $this->assertEquals($expectedResult, $actualResult);
    }

    private function prepareGameObject(string $shipName, string $simplePosition): DefaultShip
    {
        $ship = new DefaultShip();

        $ship->setProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION, $simplePosition);
        $ship->setProperty(SpaceObjectPropertyEnum::SPACE_OBJECT_NAME, $shipName);

        return $ship;
    }

    public static function providerAvoidCollision(): array
    {
        return [
            [
                'parameterList' => [
                    'Construction ship' => '1',
                    'Science ship' => '1',
                    'Colony ship' => '1',
                    'Transport ship' => '1',
                ],
            ],
        ];
    }

    public static function providerNoCollision(): array
    {
        return [
            [
                'parameterList' => [
                    'Construction ship' => '1',
                    'Science ship' => '2',
                    'Colony ship' => '3',
                    'Transport ship' => '4',
                ],
            ],
        ];
    }

    public static function providerSeveralCollision(): array
    {
        return [
            [
                'parameterList' => [
                    [
                        'Construction ship' => '1',
                        'Science ship' => '1',
                    ],
                    [
                        'Colony ship' => '1',
                        'Transport ship' => '1',
                    ]
                ],
            ],
        ];
    }
}
