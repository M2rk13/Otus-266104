<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\InterpretException;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\Interpreter\InterpretCommandInterface;
use App\Maintenance\Interpreter\InterpretObject;
use App\Maintenance\Interpreter\InterpretObjectParameter;
use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;
use App\Reposition\Coordinates;
use App\Reposition\MovableAdapter;
use App\SpaceObjects\Ship\Torpedo;
use App\SpaceObjects\SpaceObject;
use Exception;

class InterpretFireCommand implements InterpretCommandInterface
{
    /**
     * @throws InterpretException
     * @throws PropertyNotFoundException
     * @throws Exception
     */
    public function execute(IoC $ioC, InterpretObject $order, Queue $queue): void
    {
        /** @var SpaceObject $spaceObject */
        $spaceObject = $ioC->resolve($order->getObjectId());

        $torpedo = new Torpedo();
        $position = clone $spaceObject->getProperty(SpaceObjectPropertyEnum::POSITION);

        $baseVelocity = new Coordinates(20, 0);

        $angularPosition = $spaceObject->getProperty(SpaceObjectPropertyEnum::ANGULAR_POSITION);
        $shipCorrectedVelocity = $baseVelocity->rotateByAngular($angularPosition);

        $fireAngular = $this->getAngular($order);
        $resultVelocity = $shipCorrectedVelocity->rotateByAngular($fireAngular);

        $torpedo->setProperty(SpaceObjectPropertyEnum::POSITION, $position);
        $torpedo->setProperty(SpaceObjectPropertyEnum::VELOCITY, $resultVelocity);

        $key = 'torpedo' . random_int(1, 1000) . $order->getObjectId();

        $ioC->resolve(IoC::SCOPES_CURRENT, 'system');
        $ioC->resolve(IoC::IOC_REGISTER, $key, function () use ($torpedo) {
            return $torpedo;
        });

        $adapter = new MovableAdapter($torpedo);
        $command = new MoveCommand($adapter);

        $queue->push($command);
    }

    /**
     * @throws InterpretException
     */
    private function getAngular(InterpretObject $order): int
    {
        /** @var InterpretObjectParameter $parameter */
        foreach ($order->getParameterList() as $parameter) {
            if ($parameter->type === SpaceObjectPropertyEnum::ANGULAR_POSITION) {
                return $parameter->value;
            }
        }

        throw new InterpretException();
    }
}
