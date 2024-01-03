<?php

declare(strict_types=1);

namespace App\HomeWork\PartFive;

interface IoCInterface
{
    public function resolve(string $key, ...$args): mixed;
}
