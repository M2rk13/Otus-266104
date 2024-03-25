<?php

declare(strict_types=1);

namespace App\ProjectCascade\Billing\PaymentSystem\BillingBundle;

interface PaymentSystemProviderInterface
{
    public function requestPayment(string $transactionId): ?string;
    public static function getName(): string;
}
