<?php

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderFourBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Enum\ProviderEnum;
use App\ProjectCascade\Exception\PaymentProviderException;

class PaymentSystemProviderFour implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        throw new PaymentProviderException($transactionId);
    }

    public function getName(): string
    {
        return ProviderEnum::FOUR;
    }
}
