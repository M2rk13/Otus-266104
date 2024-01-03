<?php

declare(strict_types=1);

namespace App\Command;

use App\Reposition\FuelBurnableAdapter;
use App\Reposition\MovableAdapter;

class MoveWithFuelCommand implements CommandInterface
{
    public function __construct(
        private readonly FuelBurnableAdapter $fuelBurnableAdapter,
        private readonly MovableAdapter $movableAdapter,
    ) {
    }

    public function execute(): void
    {
        $checkFuelCommand = new CheckFuelCommand($this->fuelBurnableAdapter);
        $burnFuelCommand = new BurnFuelCommand($this->fuelBurnableAdapter);
        $moveCommand = new MoveCommand($this->movableAdapter);

        $macroCommand = new MacroCommand([$checkFuelCommand, $burnFuelCommand, $moveCommand]);
        $macroCommand->execute();
    }
}
