<?php

declare(strict_types=1);

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderFourBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Exception\PaymentProviderException;

class PaymentSystemProviderFour implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        throw new PaymentProviderException($transactionId);
    }

    public static function getName(): string
    {
        return 'four';
    }
}
