<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\SpaceObjectPropertyEnum;
use App\Maintenance\Interpreter\InterpretCommandInterface;
use App\Maintenance\Interpreter\InterpretObject;
use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;
use App\Reposition\Coordinates;
use App\Reposition\VelocityChangeableAdapter;
use App\SpaceObjects\SpaceObject;

class InterpretStopCommand implements InterpretCommandInterface
{
    public function execute(IoC $ioC, InterpretObject $order, Queue $queue): void
    {
        /** @var SpaceObject $spaceObject */
        $spaceObject = $ioC->resolve($order->getObjectId());

        $velocity = $spaceObject->getProperty(SpaceObjectPropertyEnum::VELOCITY);
        $velocityKey = sprintf(self::IOC_KEY_FORMAT, $order->getObjectId(), SpaceObjectPropertyEnum::VELOCITY);

        $ioC->resolve(IoC::IOC_REGISTER, $velocityKey, function () use ($velocity) {
            return $velocity;
        });

        $adapter = new VelocityChangeableAdapter($spaceObject);
        $command = new ChangeVelocityCommand($adapter, new Coordinates(0, 0));

        $queue->push($command);
    }
}
