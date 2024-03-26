<?php

declare(strict_types=1);

namespace App\ProjectCascade\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

interface RabbitClientInterface
{
    public function put(
        QueueMessageDto $messageDto,
        array $properties = []
    ): void;

    public function get(
        string $queueName,
    ): ?AMQPMessage;

    public function repeat(
        AMQPMessage $message,
        string $queueName,
    ): void;
}
