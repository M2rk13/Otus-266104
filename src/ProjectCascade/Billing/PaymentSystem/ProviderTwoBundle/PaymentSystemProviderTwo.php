<?php

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderTwoBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Enum\ProviderEnum;
use App\ProjectCascade\Exception\PaymentProviderException;
use PHPUnit\Logging\Exception;

class PaymentSystemProviderTwo implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        throw new PaymentProviderException($transactionId);
    }


    public function getName(): string
    {
        return ProviderEnum::TWO;
    }
}
