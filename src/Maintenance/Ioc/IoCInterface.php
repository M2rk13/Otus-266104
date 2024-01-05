<?php

declare(strict_types=1);

namespace App\Maintenance\Ioc;

interface IoCInterface
{
    public function resolve(string $key, ...$args): mixed;
}
