<?php

namespace App\ProjectCascade\Service;

use App\ProjectCascade\Enum\QueueMessageTypeEnum;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeMessageDto;
use App\ProjectCascade\RabbitMQ\QueueMessageDto;
use App\ProjectCascade\RabbitMQ\RabbitClient;
use App\ProjectCascade\RabbitMQ\RabbitDtoInterface;
use JsonException;

class QueueService
{
    private RabbitClient $rabbitClient;

    public function __construct()
    {
        $this->rabbitClient = new RabbitClient();
    }

    /**
     * @throws JsonException
     */
    public function putQueueMessage(
        RabbitDtoInterface $rabbitDto,
        string $routingKey,
    ): void {
        $messageDto = new QueueMessageDto(
            [
                'dto' => $rabbitDto,
                'className' => $rabbitDto::class,
                'routingKey' => $routingKey
            ]
        );

        $this->rabbitClient->put($messageDto);
    }

    /**
     * @throws JsonException
     */
    public function putCascadePaymentMessage(CascadeMessageDto $rabbitDto): void
    {
        $this->putQueueMessage(
            $rabbitDto,
            QueueMessageTypeEnum::CASCADE_PROVIDER_OPERATION
        );
    }
}
