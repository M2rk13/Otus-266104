<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use App\ProjectCascade\RabbitMQ\RabbitDtoInterface;

interface QueueServiceInterface
{
    public function putQueueMessage(RabbitDtoInterface $rabbitDto, string $routingKey): void;
}
