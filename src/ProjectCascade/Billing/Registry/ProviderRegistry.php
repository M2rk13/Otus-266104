<?php

namespace App\ProjectCascade\Billing\Registry;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Exception\PaymentProviderNotFoundException;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerInterface;

class ProviderRegistry
{
    /** @var GateWayHandlerInterface[] */
    private array $providerPaymentSystem;

    public function __construct()
    {
        $this->providerPaymentSystem = $GLOBALS['provider_list'];
    }

    /**
     * @throws PaymentProviderNotFoundException
     */
    public function getProvider(string $providerName): PaymentSystemProviderInterface
    {
        $name = $this->providerPaymentSystem[$providerName] ?? null;

        if (empty($name)) {
            throw new PaymentProviderNotFoundException($providerName);
        }

        return new $name();
    }
}
