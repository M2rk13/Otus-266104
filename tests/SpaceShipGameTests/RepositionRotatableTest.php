<?php

declare(strict_types=1);

namespace App\Tests\SpaceShipGameTests;

use App\Command\RotateCommand;
use App\Enum\RepositionPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Reposition\RotatableAdapter;
use App\SpaceObjects\Ship\DefaultShip;
use PHPUnit\Framework\TestCase;

class RepositionRotatableTest extends TestCase
{
    /**
     * @dataProvider rotateDataProvider
     *
     * @throws PropertyNotFoundException
     */
    public function testRotate(float $startAngular, float $rotateAngular, float $expectedValue): void
    {
        $ship = new DefaultShip();
        $ship->setProperty(RepositionPropertyEnum::ANGULAR_POSITION, $startAngular);
        $ship->setProperty(RepositionPropertyEnum::ANGULAR_CHANGE, $rotateAngular);

        $rotatableShip = new RotatableAdapter($ship);

        $rotateCommand = new RotateCommand($rotatableShip);
        $rotateCommand();

        self::assertSame($expectedValue, $ship->getProperty(RepositionPropertyEnum::ANGULAR_POSITION));
    }

    public static function rotateDataProvider(): array
    {
        return [
            'validRotate' => [
                'startAngular' => 0,
                'rotateAngular' => 45,
                'expectedValue' => 45,
            ],
            'validBackRotate' => [
                'startAngular' => 90,
                'rotateAngular' => -100,
                'expectedValue' => -10,
            ],
        ];
    }

    /**
     * @dataProvider rotateErrorsProvider
     *
     * @throws PropertyNotFoundException
     */
    public function testRotateErrors(
        string $object,
        ?int $startAngular,
        ?int $rotateAngular,
        string $expectedException
    ): void
    {
        $this->expectException($expectedException);
        $ship = new $object();

        if ($startAngular !== null) {
            $ship->setProperty(RepositionPropertyEnum::ANGULAR_POSITION, $startAngular);
        }


        if ($rotateAngular !== null) {
            $ship->setProperty(RepositionPropertyEnum::ANGULAR_CHANGE, $rotateAngular);
        }

        $rotatableShip = new RotatableAdapter($ship);
        $rotateCommand = new RotateCommand($rotatableShip);
        $rotateCommand();
    }

    public static function rotateErrorsProvider(): array
    {
        return [
            'noStartValue' => [
                'object' => DefaultShip::class,
                'startAngular' => null,
                'rotateAngular' => 45,
                'expectedException' => PropertyNotFoundException::class,
            ],
            'noMoveValue' => [
                'object' => DefaultShip::class,
                'startAngular' => 0,
                'rotateAngular' => null,
                'expectedException' => PropertyNotFoundException::class,
            ],
        ];
    }
}
