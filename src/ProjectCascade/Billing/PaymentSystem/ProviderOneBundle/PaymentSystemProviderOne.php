<?php

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderOneBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Exception\PaymentProviderException;

class PaymentSystemProviderOne implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        throw new PaymentProviderException($transactionId);
    }

    public static function getName(): string
    {
        return 'one';
    }
}