<?php

declare(strict_types=1);

namespace App\Reposition;

final class Coordinates
{
    public function __construct(
        private readonly float $x = 0,
        private readonly float $y = 0,
    )
    {
    }

    public function add(self $vector): self
    {
        return new self(
            x: $this->x + $vector->x,
            y: $this->y + $vector->y
        );
    }
}
