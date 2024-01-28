<?php

declare(strict_types=1);

namespace App\RabbitMq;

use Exception;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitQueue
{
    private string $queueName;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly AMQPChannel $channel,
        string $queueName = 'default_queue',
    ) {
        $channel->queue_declare($queueName,
            false,
            true,
            false,
            false,
            false,
            new AMQPTable(['x-queue-type' => 'quorum'])
        );

        $this->queueName = $queueName;
    }

    /**
     * @throws JsonException
     */
    public function sendMessage(RabbitMessage $rabbitMessage): void
    {
        $msg = new AMQPMessage(
            json_encode((array) $rabbitMessage, JSON_THROW_ON_ERROR),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT],
        );
        $this->channel->basic_publish($msg, '', $this->queueName);
    }

    /**
     * @throws JsonException
     */
    public function extractNextMessage(): ?RabbitMessage
    {
        $msg = $this->channel->basic_get($this->queueName, true)?->body;

        if ($msg === null) {
            return null;
        }

        $paramList = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);

        return new RabbitMessage(...$paramList);
    }
}
