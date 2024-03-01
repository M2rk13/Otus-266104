<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\SpaceObjectPropertyEnum;
use App\Maintenance\Interpreter\InterpretCommandInterface;
use App\Maintenance\Interpreter\InterpretObject;
use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;
use App\Reposition\VelocityChangeableAdapter;
use App\SpaceObjects\SpaceObject;

class InterpretStartCommand implements InterpretCommandInterface
{
    public function execute(IoC $ioC, InterpretObject $order, Queue $queue): void
    {
        /** @var SpaceObject $spaceObject */
        $spaceObject = $ioC->resolve($order->getObjectId());

        $velocityKey = sprintf(self::IOC_KEY_FORMAT, $order->getObjectId(), SpaceObjectPropertyEnum::VELOCITY);
        $velocity = $ioC->resolve($velocityKey);

        $adapter = new VelocityChangeableAdapter($spaceObject);
        $command = new ChangeVelocityCommand($adapter, $velocity);

        $queue->push($command);
    }
}
