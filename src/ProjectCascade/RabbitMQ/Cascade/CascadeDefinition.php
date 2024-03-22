<?php

namespace App\ProjectCascade\RabbitMQ\Cascade;

use App\ProjectCascade\Enum\QueueEnum;
use App\ProjectCascade\Enum\QueueMessageTypeEnum;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class CascadeDefinition
{
    public const QUEUE_NAME = QueueEnum::CASCADE_QUEUE;
    private const ENTRY_POINT = QueueEnum::MAIN_EXCHANGE;
    private const ROUTE_LIST = [QueueMessageTypeEnum::CASCADE_PROVIDER_OPERATION];

    public function init(AMQPStreamConnection $connection): void
    {
        $channel = $connection->channel();

        $channel->queue_declare(
            self::QUEUE_NAME,
            false,
            true,
            false,
            false,
            false,
            new AMQPTable(['x-queue-type' => 'quorum'])
        );

        foreach (self::ROUTE_LIST as $route) {
            $channel->queue_bind(self::QUEUE_NAME, self::ENTRY_POINT, $route);
        }
    }
}