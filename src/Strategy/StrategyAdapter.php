<?php

namespace App\Strategy;

use App\Command\BurnFuelCommand;
use App\Command\ChangeVelocityCommand;
use App\Command\CheckFuelCommand;
use App\Command\MoveCommand;
use App\Command\MoveWithFuelCommand;
use App\Command\RotateCommand;
use App\Command\RotateWithChangeVelocityCommand;
use App\Reposition\FuelBurnableAdapter;
use App\Reposition\MovableAdapter;
use App\Reposition\RotatableAdapter;
use App\Reposition\VelocityChangeableAdapter;
use App\SpaceObjects\SpaceObjectInterface;

class StrategyAdapter
{
    public function __invoke(SpaceObjectInterface $spaceObject, string $commandName): array
    {
        return match ($commandName) {
            BurnFuelCommand::class, CheckFuelCommand::class => [new FuelBurnableAdapter($spaceObject)],
            ChangeVelocityCommand::class => [new VelocityChangeableAdapter($spaceObject)],
            MoveCommand::class => [new MovableAdapter($spaceObject)],
            MoveWithFuelCommand::class => [new FuelBurnableAdapter($spaceObject), new MovableAdapter($spaceObject)],
            RotateCommand::class => [new RotatableAdapter($spaceObject)],
            RotateWithChangeVelocityCommand::class => [$spaceObject],
        };
    }
}
