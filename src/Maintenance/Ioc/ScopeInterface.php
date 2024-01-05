<?php

declare(strict_types=1);

namespace App\Maintenance\Ioc;

interface ScopeInterface
{
    public function getName(): string;
    public function addDependency(string $key, callable $dependencyCallable): void;
    public function resolve(string $key, ...$args): mixed;
}
