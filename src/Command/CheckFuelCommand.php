<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\NotEnoughFuelException;
use App\Reposition\FuelBurnableInterface;

class CheckFuelCommand implements CommandInterface
{
    public function __construct(
        private readonly FuelBurnableInterface $fuelBurnable,
    ) {
    }

    /**
     * @throws NotEnoughFuelException
     */
    public function execute(): void
    {
        $nextFuelLevel = $this->fuelBurnable->getFuelLevel() - $this->fuelBurnable->getFuelConsumption();

        if ($nextFuelLevel < 0) {
            throw new NotEnoughFuelException();
        }
    }
}
