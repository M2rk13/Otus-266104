<?php

declare(strict_types=1);

namespace App\Maintenance\Ioc;

use RuntimeException;

class IoC implements IoCInterface
{
    public function __construct(
        private readonly ScopeService $scopeService = new ScopeService(),
    ) {
    }

    public const SCOPES_NEW = 'Scopes.New';
    public const SCOPES_CURRENT = 'Scopes.Current';
    public const IOC_REGISTER = 'IoC.Register';

    /**
     * @throws RuntimeException
     */
    public function resolve(string $key, ...$args): mixed
    {
        switch ($key) {
            case self::SCOPES_NEW:
                $this->scopeService->createScope($args[0]);

                break;
            case self::SCOPES_CURRENT:
                $this->scopeService->setScope($args[0]);

                break;
            case self::IOC_REGISTER:
                $this->scopeService->getScope()->addDependency($args[0], $args[1]);

                break;
            default:
                return $this->scopeService->getScope()->resolve($key, ...$args);
        }

        return true;
    }
}
