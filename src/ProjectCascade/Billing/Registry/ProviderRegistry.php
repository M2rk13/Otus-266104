<?php

namespace App\ProjectCascade\Billing\Registry;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Billing\PaymentSystem\ProviderCascadeBundle\PaymentSystemProviderCascade;
use App\ProjectCascade\Billing\PaymentSystem\ProviderFiveBundle\PaymentSystemProviderFive;
use App\ProjectCascade\Billing\PaymentSystem\ProviderFourBundle\PaymentSystemProviderFour;
use App\ProjectCascade\Billing\PaymentSystem\ProviderOneBundle\PaymentSystemProviderOne;
use App\ProjectCascade\Billing\PaymentSystem\ProviderThreeBundle\PaymentSystemProviderThree;
use App\ProjectCascade\Billing\PaymentSystem\ProviderTwoBundle\PaymentSystemProviderTwo;
use App\ProjectCascade\Enum\ProviderEnum;
use App\ProjectCascade\Exception\PaymentProviderNotFoundException;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerInterface;

class ProviderRegistry
{
    // это реестр, который можно организовать разными способами автозагрузки,
    // условно обозначим хардкодом
    /** @var GateWayHandlerInterface[] */
    private array $providerPaymentSystem = [
        ProviderEnum::ONE => PaymentSystemProviderOne::class,
        ProviderEnum::TWO => PaymentSystemProviderTwo::class,
        ProviderEnum::THREE => PaymentSystemProviderThree::class,
        ProviderEnum::FOUR => PaymentSystemProviderFour::class,
        ProviderEnum::FIVE => PaymentSystemProviderFive::class,
        ProviderEnum::CASCADE => PaymentSystemProviderCascade::class,
    ];

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
