<?php

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderThreeBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Enum\ProviderEnum;
use App\ProjectCascade\Service\IdGenerator;

class PaymentSystemProviderThree implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        return 'providerThree-externalTransactionId-' . IdGenerator::generateUniqueId();
    }


    public function getName(): string
    {
        return ProviderEnum::THREE;
    }
}
