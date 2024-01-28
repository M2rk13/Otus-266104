<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Command\ConsumerRunCommand;
use App\Command\MoveCommand;
use App\Command\QueueSoftStopCommand;
use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\Ioc\IoC;
use App\Queue\Consumer;
use App\Queue\Queue;
use App\RabbitMq\RabbitFactory;
use App\RabbitMq\RabbitMessageParameter;
use App\Reposition\Coordinates;
use App\SpaceObjects\Ship\DefaultShip;
use App\SpaceObjects\SpaceObject;
use App\Tests\Helpers\SweetsThief;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class RabbitMqTest extends TestCase
{
    private const SCOPE_ID = 'testGame';
    private const OBJECT_ID = 'testShip1';

    /**
     * @dataProvider providerRabbitGameTest
     *
     * @throws JsonException
     * @throws PropertyNotFoundException
     */
    public function testRabbitGame(
        array $startCoordinates,
        array $startVelocity,
        array $velocityToMove,
        array $expectedResult,
    ): void
    {
        $ioC = $this->prepareGame($startCoordinates, $startVelocity);
        $parameter = new RabbitMessageParameter(
            SpaceObjectPropertyEnum::VELOCITY,
            $velocityToMove
        );

        $rabbitFactory = $this->prepareRabbit($ioC, [
            [
                'scopeId' => self::SCOPE_ID,
                'objectId' => self::OBJECT_ID,
                'commandClass' => MoveCommand::class,
                'parameterList' => [$parameter],
            ]
        ]);

        $rabbitFactory->getConsumer('test')->processNextMessage();

        $queue = $ioC->resolve(Queue::class);
        $consumer = new Consumer($queue);
        $consumer->setQueue($queue);
        $processor = new ConsumerRunCommand($consumer);
        $queue->push(new QueueSoftStopCommand($processor));

        $processor->execute();

        /** @var SpaceObject $ship */
        $ship = $ioC->resolve(self::OBJECT_ID);
        /** @var Coordinates $coordinates */
        $coordinates = $ship->getProperty(SpaceObjectPropertyEnum::POSITION);

        $coordinatesThief = new SweetsThief($coordinates);

        $result = [
            'x' => $coordinatesThief->steeleSweets('x'),
            'y' => $coordinatesThief->steeleSweets('y'),
        ];

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @throws JsonException
     */
    private function prepareRabbit(IoC $ioC, array $messageBodyList): RabbitFactory
    {
        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['basic_get', 'queue_declare'])
            ->getMock()
        ;

        $rabbitMessageList = [];

        foreach ($messageBodyList as $messageBody) {
            $rabbitMessageList[] = new AMQPMessage(json_encode($messageBody, JSON_THROW_ON_ERROR));
        }

        $channel
            ->method('basic_get')
            ->willReturnOnConsecutiveCalls(...$rabbitMessageList);

        $channel
            ->method('queue_declare')
            ->willReturnOnConsecutiveCalls(['test'])
        ;

        $rabbitFactory = $this->getMockBuilder(RabbitFactory::class)
            ->onlyMethods(['getChannel'])
            ->setConstructorArgs([$ioC])
            ->getMock()
        ;
        $rabbitFactory
            ->method('getChannel')
            ->willReturnOnConsecutiveCalls($channel)
        ;

        return $rabbitFactory;
    }

    private function prepareGame(
        array $startCoordinates,
        array $startVelocity,
    ): IoC {
        $ioC = new IoC;

        $ioC->resolve(IoC::SCOPES_NEW, self::SCOPE_ID);
        $ioC->resolve(IoC::SCOPES_CURRENT, self::SCOPE_ID);

        $position = new Coordinates(...$startCoordinates);
        $velocity = new Coordinates(...$startVelocity);

        $ship = new DefaultShip();
        $ship->setProperty(SpaceObjectPropertyEnum::POSITION, $position);
        $ship->setProperty(SpaceObjectPropertyEnum::VELOCITY, $velocity);

        $ioC->resolve(IoC::IOC_REGISTER, self::OBJECT_ID, function () use ($ship) {
            return $ship;
        });

        $queue = new Queue();

        $ioC->resolve(IoC::IOC_REGISTER, Queue::class, function () use ($queue) {
            return $queue;
        });

        return $ioC;
    }

    public static function providerRabbitGameTest(): array
    {
        return [
            'successMove' => [
                'startCoordinates' => [
                    'x' => 12,
                    'y' => 5,
                ],
                'startVelocity' => [
                    'x' => 0,
                    'y' => 0,
                ],
                'velocityToMove' => [
                    'x' => -7,
                    'y' => 5,
                ],
                'expectedResult' => [
                    'x' => 5,
                    'y' => 10,
                ]
            ]
        ];
    }
}
