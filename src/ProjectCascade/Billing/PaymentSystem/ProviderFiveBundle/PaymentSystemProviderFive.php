<?php

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderFiveBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Enum\ProviderEnum;
use App\ProjectCascade\Service\IdGenerator;

class PaymentSystemProviderFive implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        return 'providerFive-externalTransactionId-' . IdGenerator::generateUniqueId();
    }


    public function getName(): string
    {
        return ProviderEnum::FIVE;
    }
}
