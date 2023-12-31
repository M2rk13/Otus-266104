<?php

declare(strict_types=1);

namespace App\Maintenance\Ioc;

interface ScopesInterface
{
    public function createScope(string $scopeName): void;
    public function getScope(): ScopeInterface;
    public function setScope(string $scopeName): void;
}
