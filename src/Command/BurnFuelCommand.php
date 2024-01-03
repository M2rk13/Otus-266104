<?php

declare(strict_types=1);

namespace App\Command;

use App\Reposition\FuelBurnableInterface;

class BurnFuelCommand implements CommandInterface
{
    public function __construct(
        private readonly FuelBurnableInterface $fuelBurnable,
    ) {
    }

    public function execute(): void
    {
        $nextFuelLevel = $this->fuelBurnable->getFuelLevel() - $this->fuelBurnable->getFuelConsumption();
        $this->fuelBurnable->setFuelLevel($nextFuelLevel);
    }
}
