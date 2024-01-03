<?php

declare(strict_types=1);

namespace App\Command;

use App\Reposition\Coordinates;
use App\Reposition\VelocityChangeableInterface;

class ChangeVelocityCommand implements CommandInterface
{
    public function __construct(
        private readonly VelocityChangeableInterface $velocityChangeable,
        private readonly Coordinates $newVelocity,
    ) {
    }

    public function execute(): void
    {
        $this->velocityChangeable->setVelocity($this->newVelocity);
    }
}
