<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\SpaceObjectPropertyEnum;
use App\Reposition\Coordinates;
use App\Reposition\RotatableAdapter;
use App\Reposition\VelocityChangeableAdapter;
use App\SpaceObjects\SpaceObjectInterface;

class RotateWithChangeVelocityCommand implements CommandInterface
{
    public function __construct(
        private readonly SpaceObjectInterface $object,
    ) {
    }

    public function execute(): void
    {
        $commandList = [];
        $angular = $this->object->getProperty(SpaceObjectPropertyEnum::ANGULAR_CHANGE);

        $rotatableAdapter = new RotatableAdapter($this->object);
        $commandList[] = new RotateCommand($rotatableAdapter);

        if ($this->object->isPropertySet(SpaceObjectPropertyEnum::VELOCITY)) {
            /** @var Coordinates $velocity */
            $velocity = $this->object->getProperty(SpaceObjectPropertyEnum::VELOCITY);
            $newVelocity = $velocity->rotateByAngular($angular);

            $velocityAdapter = new VelocityChangeableAdapter($this->object);
            $commandList[] = new ChangeVelocityCommand($velocityAdapter, $newVelocity);
        }

        $macroCommand = new MacroCommand($commandList);
        $macroCommand->execute();
    }
}
