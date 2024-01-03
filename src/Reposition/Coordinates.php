<?php

declare(strict_types=1);

namespace App\Reposition;

final class Coordinates
{
    private const DECIMALS_SCALE = 3;

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

    public function rotateByAngular(float $angular): self
    {
        $angular = deg2rad($angular);

        $x = $this->x * cos($angular) - $this->y * sin($angular);
        $y = $this->x * sin($angular) + $this->y * cos($angular);

        return new self(
            x: (float) number_format($x, self::DECIMALS_SCALE, '.', ''),
            y: (float) number_format($y, self::DECIMALS_SCALE, '.', '')
        );
    }
}
