<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\SpaceObjectPropertyEnum;
use App\Maintenance\Ioc\IoC;
use App\RabbitMq\RabbitMessage;
use App\RabbitMq\RabbitMessageParameter;
use App\Reposition\Coordinates;
use App\SpaceObjects\SpaceObjectInterface;
use App\Strategy\StrategyAdapter;

class InterpretCommand implements CommandInterface
{
    public function __construct(
        private readonly IoC $ioC,
        private readonly RabbitMessage $msg
    ) {
    }

    public function execute(): void
    {
        $msg = $this->msg;

        /** @var SpaceObjectInterface $spaceObject */
        $spaceObject = $this->ioC->resolve($msg->objectId);

        /** @var RabbitMessageParameter $parameter */
        foreach ($msg->parameterList as $parameter) {
            $this->setParameter($spaceObject, new RabbitMessageParameter(...$parameter));
        }

        $commandClass = $msg->commandClass;
        $commandArgList = (new StrategyAdapter)($spaceObject, $commandClass);

        /** @var CommandInterface $command */
        $command = new ($commandClass)(...$commandArgList);
        $command->execute();
    }

    private function setParameter(
        SpaceObjectInterface $spaceObject,
        RabbitMessageParameter $parameter,
    ): void {
        $value = match ($parameter->type) {
            SpaceObjectPropertyEnum::VELOCITY, SpaceObjectPropertyEnum::POSITION => new Coordinates(...$parameter->value),
            default => $parameter->value,
        };

        $spaceObject->setProperty($parameter->type, $value);
    }
}
