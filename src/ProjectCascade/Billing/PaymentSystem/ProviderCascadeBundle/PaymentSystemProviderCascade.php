<?php

declare(strict_types=1);

namespace App\ProjectCascade\Billing\PaymentSystem\ProviderCascadeBundle;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Enum\QueueMessageTypeEnum;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeMessageDto;
use App\ProjectCascade\Service\IoCResolverService;
use App\ProjectCascade\Service\QueueServiceInterface;

class PaymentSystemProviderCascade implements PaymentSystemProviderInterface
{
    private QueueServiceInterface $queueService;

    public function __construct()
    {
        /** @var QueueServiceInterface $queueService */
        $queueService = IoCResolverService::getClass(QueueServiceInterface::class);
        $this->queueService = $queueService;
    }

    public function requestPayment(string $transactionId): ?string
    {
        $dto = new CascadeMessageDto(['transactionId' => $transactionId]);

        $this->queueService->putQueueMessage($dto, QueueMessageTypeEnum::CASCADE_PROVIDER_OPERATION);

        return null;
    }

    public static function getName(): string
    {
        return 'cascade';
    }
}
