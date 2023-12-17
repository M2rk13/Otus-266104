<?php

declare(strict_types=1);

namespace App\Command;

use App\Reposition\MovableInterface;

class MoveCommand implements CommandInterface
{
    private MovableInterface $movable;

    public function __construct(MovableInterface $movable)
    {
        $this->movable = $movable;
    }

    public function __invoke(): void
    {
        $velocity = $this->movable->getVelocity();
        $position = $this->movable->getPosition();

        $this->movable->setPosition($position->add($velocity));
    }
}
