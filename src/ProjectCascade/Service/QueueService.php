<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use App\ProjectCascade\RabbitMQ\QueueMessageDto;
use App\ProjectCascade\RabbitMQ\RabbitClientInterface;
use App\ProjectCascade\RabbitMQ\RabbitDtoInterface;

final class QueueService implements QueueServiceInterface
{
    private RabbitClientInterface $rabbitClient;

    public function __construct()
    {
        /** @var RabbitClientInterface $rabbitClient */
        $rabbitClient = IoCResolverService::getClass(RabbitClientInterface::class);
        $this->rabbitClient = $rabbitClient;
    }

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
}
