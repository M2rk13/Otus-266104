<?php

declare(strict_types=1);

namespace App\Maintenance\Ioc;

use RuntimeException;

class ScopeService implements ScopesInterface
{
    /** @var $scopeList Scope[] */
    private array $scopeList = [];

    private string $defaultScopeName = 'default';

    public function createScope(string $scopeName): void
    {
        $this->scopeList[$scopeName] = new Scope($scopeName);
    }

    public function getScope(): Scope
    {
        if (!isset($this->scopeList[$this->defaultScopeName])) {
            $this->createScope($this->defaultScopeName);
        }

        return $this->scopeList[$this->defaultScopeName];
    }

    /**
     * @throws RuntimeException
     */
    public function setScope(string $scopeName): void
    {
        if (!isset($this->scopeList[$scopeName])) {
            throw new RuntimeException(sprintf('Scope [%s] doest not exists', $scopeName));
        }

        $this->defaultScopeName = $scopeName;
    }
}
