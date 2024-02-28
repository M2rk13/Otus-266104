<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Command\CommandInterface;
use App\Enum\SpaceGameActionEnum;
use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\ProjectException;
use App\Maintenance\Interpreter\Interpreter;
use App\Maintenance\Interpreter\InterpretObject;
use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;
use App\Reposition\Coordinates;
use App\SpaceObjects\Ship\DefaultShip;
use App\SpaceObjects\SpaceObject;
use App\Tests\Helpers\SweetsThief;
use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{
    private const SCOPE_PLAYER_1 = 'scopePlayer1';
    private const SCOPE_PLAYER_2 = 'scopePlayer2';
    private const AUTH_PLAYER_1 = 'authPlayer1';
    private const AUTH_PLAYER_2 = 'authPlayer2';
    private const SHIP_PLAYER_1 = 'scopePlayer1';
    private const SHIP_PLAYER_2 = 'scopePlayer2';
    private const AUTH_KEEPER = 'authKeeper';

    public function testInterpreterStop(): void
    {
        $startCoordinates = [
            'x' => 1.0,
            'y' => 1.0,
        ];
        $arrayStartVelocity = [
            'x' => 5.0,
            'y' => -2.0,
        ];

        $ioC = $this->prepareGameForTwoPlayer($startCoordinates, $arrayStartVelocity);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_PLAYER_1);

        /** @var SpaceObject $ship */
        $ship = $ioC->resolve(self::SHIP_PLAYER_1);

        $velocityFromStart = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $coordinatesThief = new SweetsThief($velocityFromStart);

        $arrayVelocityFrom = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        $order = new InterpretObject(
            self::AUTH_PLAYER_1,
            self::SHIP_PLAYER_1,
            SpaceGameActionEnum::ACTION_STOP_MOVE
        );

        $queue = new Queue();

        $interpreter = new Interpreter($ioC, $queue);
        $interpreter->interpret($order);

        /** @var CommandInterface $cmd */
        $cmd = $queue->extract();
        $cmd->execute();

        $velocityTo = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $coordinatesThief = new SweetsThief($velocityTo);

        $arrayVelocityTo = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        self::assertSame($arrayStartVelocity, $arrayVelocityFrom);
        self::assertNotSame($arrayVelocityTo, $arrayVelocityFrom);
    }

    public function testInterpreterStartAfterStop(): void
    {
        $startCoordinates = [
            'x' => 1.0,
            'y' => 1.0,
        ];
        $arrayStartVelocity = [
            'x' => 5.0,
            'y' => -2.0,
        ];

        $ioC = $this->prepareGameForTwoPlayer($startCoordinates, $arrayStartVelocity);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_PLAYER_1);

        /** @var SpaceObject $ship */
        $ship = $ioC->resolve(self::SHIP_PLAYER_1);

        $velocityFromStart = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $coordinatesThief = new SweetsThief($velocityFromStart);

        $arrayVelocityFrom = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        $order = new InterpretObject(
            self::AUTH_PLAYER_1,
            self::SHIP_PLAYER_1,
            SpaceGameActionEnum::ACTION_STOP_MOVE
        );

        $queue = new Queue();

        $interpreter = new Interpreter($ioC, $queue);
        $interpreter->interpret($order);

        /** @var CommandInterface $cmd */
        $cmd = $queue->extract();
        $cmd->execute();

        $newOrder = new InterpretObject(
            self::AUTH_PLAYER_1,
            self::SHIP_PLAYER_1,
            SpaceGameActionEnum::ACTION_START_MOVE
        );
        $interpreter->interpret($newOrder);

        /** @var CommandInterface $cmd */
        $cmd = $queue->extract();
        $cmd->execute();

        $velocityTo = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $coordinatesThief = new SweetsThief($velocityTo);

        $arrayVelocityTo = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        self::assertSame($arrayStartVelocity, $arrayVelocityFrom);
        self::assertSame($arrayVelocityTo, $arrayVelocityFrom);
    }

    public function testInterpreterStart(): void
    {
        $startCoordinates = [
            'x' => 1.0,
            'y' => 1.0,
        ];
        $arrayStartVelocity = [
            'x' => 0.0,
            'y' => 0.0,
        ];

        $ioC = $this->prepareGameForTwoPlayer($startCoordinates, $arrayStartVelocity);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_PLAYER_1);

        $key = self::SHIP_PLAYER_1 . '[.]' . SpaceObjectPropertyEnum::VELOCITY;
        $ioC->resolve(IoC::IOC_REGISTER, $key, function () {
            return new Coordinates(-5, 2);
        });

        /** @var SpaceObject $ship */
        $ship = $ioC->resolve(self::SHIP_PLAYER_1);

        $velocityFromStart = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $coordinatesThief = new SweetsThief($velocityFromStart);

        $arrayVelocityFrom = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        $order = new InterpretObject(
            self::AUTH_PLAYER_1,
            self::SHIP_PLAYER_1,
            SpaceGameActionEnum::ACTION_START_MOVE
        );

        $queue = new Queue();

        $interpreter = new Interpreter($ioC, $queue);
        $interpreter->interpret($order);

        /** @var CommandInterface $cmd */
        $cmd = $queue->extract();
        $cmd->execute();

        $velocityTo = $ship->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $coordinatesThief = new SweetsThief($velocityTo);

        $arrayVelocityTo = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        self::assertSame($arrayStartVelocity, $arrayVelocityFrom);
        self::assertNotSame($arrayVelocityTo, $arrayVelocityFrom);
        self::assertSame($arrayVelocityTo, ['x' => -5.0, 'y' => 2.0]);
    }

    public function testOrderToOtherPlayer(): void
    {
        $ioC = $this->prepareGameForTwoPlayer([0,0], [0,0]);

        $order = new InterpretObject(
            self::AUTH_PLAYER_2,
            self::SHIP_PLAYER_1,
            SpaceGameActionEnum::ACTION_STOP_MOVE
        );

        $queue = new Queue();

        $interpreter = new Interpreter($ioC, $queue);

        $this->expectException(ProjectException::class);
        $interpreter->interpret($order);
    }

    public function testInterpreterShoot(): void
    {
        $startCoordinates = [
            'x' => 0.0,
            'y' => 0.0,
        ];
        $arrayStartVelocity = [
            'x' => 0.0,
            'y' => 0.0,
        ];

        $ioC = $this->prepareGameForTwoPlayer($startCoordinates, $arrayStartVelocity);




        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_PLAYER_1);

        /** @var SpaceObject $ship */
        $ship = $ioC->resolve(self::SHIP_PLAYER_1);
        $ship->setProperty(SpaceObjectPropertyEnum::ANGULAR_POSITION, 0);

        $order = new InterpretObject(
            self::AUTH_PLAYER_1,
            self::SHIP_PLAYER_1,
            SpaceGameActionEnum::ACTION_FIRE,
            [
                [
                    'type' => SpaceObjectPropertyEnum::ANGULAR_POSITION,
                    'value' => 90,
                ],
            ]
        );

        $queue = new Queue();

        $interpreter = new Interpreter($ioC, $queue);
        $interpreter->interpret($order);

        /** @var CommandInterface $cmd */
        $cmd = $queue->extract();

        $adapterThief = new SweetsThief($cmd);
        $adapter = $adapterThief->steeleSweets('movable');
        $spaceObjectThief = new SweetsThief($adapter);

        /** @var SpaceObject $torpedo */
        $torpedo = $spaceObjectThief->steeleSweets('object');

        $position = $torpedo->getProperty(SpaceObjectPropertyEnum::POSITION);
        $coordinatesThief = new SweetsThief($position);


        $arrayPositionBefore = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        $cmd->execute();

        $position = $torpedo->getProperty(SpaceObjectPropertyEnum::POSITION);
        $coordinatesThief = new SweetsThief($position);

        $arrayPositionAfter = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        $torpedoFromIoC = $this->extractTorpedoFromIoC($ioC);

        self::assertNotSame($arrayPositionBefore, $arrayPositionAfter);
        self::assertSame($torpedo, $torpedoFromIoC);
    }

    private function prepareGameForTwoPlayer(
        array $startCoordinates,
        array $startVelocity,
    ): IoC {
        $ioC = new IoC;

        $ioC->resolve(IoC::SCOPES_NEW, 'system');

        $ioC->resolve(IoC::SCOPES_NEW, self::SCOPE_PLAYER_1);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_PLAYER_1);

        $position = new Coordinates(...$startCoordinates);
        $velocity = new Coordinates(...$startVelocity);

        $ship = new DefaultShip();
        $ship->setProperty(SpaceObjectPropertyEnum::POSITION, $position);
        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);

        $ioC->resolve(IoC::IOC_REGISTER, self::SHIP_PLAYER_1, function () use ($ship) {
            return $ship;
        });

        $ioC->resolve(IoC::SCOPES_NEW, self::SCOPE_PLAYER_2);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_PLAYER_2);

        $position = new Coordinates(0, 0);
        $velocity = new Coordinates(0, 0);

        $ship = new DefaultShip();
        $ship->setProperty(SpaceObjectPropertyEnum::POSITION, $position);
        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);

        $ioC->resolve(IoC::IOC_REGISTER, self::SHIP_PLAYER_2, function () use ($ship) {
            return $ship;
        });

        $this->addAuthentication($ioC);

        return $ioC;
    }

    private function addAuthentication(IoC $ioC): void
    {
        $ioC->resolve(IoC::SCOPES_NEW, self::AUTH_KEEPER);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::AUTH_KEEPER);

        $ioC->resolve(IoC::IOC_REGISTER, self::AUTH_PLAYER_1, function () {
            return self::SCOPE_PLAYER_1;
        });
        $ioC->resolve(IoC::IOC_REGISTER, self::AUTH_PLAYER_2, function () {
            return self::SCOPE_PLAYER_2;
        });
    }

    private function extractTorpedoFromIoC(IoC $ioC): SpaceObject
    {
        $thief = new SweetsThief($ioC);
        $scopeService = $thief->steeleSweets('scopeService');

        $thief = new SweetsThief($scopeService);
        $scopeList = $thief->steeleSweets('scopeList');

        $scope = $scopeList['system'];
        $thief = new SweetsThief($scope);
        $dependencies = $thief->steeleSweets('dependencies');

        return array_shift($dependencies)();
    }
}
