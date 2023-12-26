<?php

declare(strict_types=1);

namespace App\Command;

use App\Reposition\Direction;
use App\Reposition\RotatableInterface;

class RotateCommand implements CommandInterface
{
    private RotatableInterface $rotatable;

    public function __construct(RotatableInterface $rotatable)
    {
        $this->rotatable = $rotatable;
    }

    public function execute(): void
    {
        $this->rotatable->setAngular(Direction::plus(
            $this->rotatable->getAngular(),
            $this->rotatable->getAngularChange()
        ));
    }
}
