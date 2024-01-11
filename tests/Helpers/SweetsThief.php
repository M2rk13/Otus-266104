<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

use Closure;

class SweetsThief
{
    public function __construct(private readonly object $kitchen)
    {
    }

    public function steeleSweets(string $sweets): mixed
    {
        $sweetsThief = static function (object $kitchen, string $sweets) {
            return $kitchen->{$sweets};
        };

        $sweetsThief = Closure::bind($sweetsThief, null, $this->kitchen);

        return $sweetsThief($this->kitchen, $sweets);
    }
}
