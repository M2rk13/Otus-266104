<?php

declare(strict_types=1);

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderThreeBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Service\IdGenerator;

class PaymentSystemProviderThree implements PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string
    {
        return 'providerThree-externalTransactionId-' . IdGenerator::generateUniqueId();
    }


    public static function getName(): string
    {
        return 'three';
    }
}
