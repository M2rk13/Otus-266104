<?php

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderCascadeBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Enum\ProviderEnum;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeMessageDto;
use App\ProjectCascade\Service\QueueService;
use JsonException;

class PaymentSystemProviderCascade implements PaymentSystemProviderInterface
{
    private QueueService $queueService;

    public function __construct()
    {
        $this->queueService = new QueueService();
    }

    /**
     * @throws JsonException
     */
    public function requestPayment(string $transactionId): ?string
    {
        $dto = new CascadeMessageDto(['transactionId' => $transactionId]);

        $this->queueService->putCascadePaymentMessage($dto);

        return null;
    }

    public function getName(): string
    {
        return ProviderEnum::CASCADE;
    }
}
