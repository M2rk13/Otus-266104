<?php

declare(strict_types=1);

namespace App\ProjectCascade\RabbitMQ;

use App\ProjectCascade\Enum\QueueEnum;
use App\ProjectCascade\Service\IoCResolverService;
use Exception;
use JsonException;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitClient implements RabbitClientInterface
{
    private AbstractChannel $connection;

    /**
     * @throws Exception
     */
    public function __construct(
    ) {
        $this->connection = IoCResolverService::getRabbitConnection();
    }

    /**
     * @throws JsonException
     */
    public function put(
        QueueMessageDto $messageDto,
        array $properties = []
    ): void
    {
        $data = $messageDto->toArray();
        $data['data'] = $messageDto->getDto()->toArray();
        $dataString = json_encode($data, JSON_THROW_ON_ERROR);
        $defaultProperties = ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];

        $message = new AMQPMessage($dataString, array_merge($defaultProperties, $properties));

        $this->connection->channel()
            ->basic_publish(
                $message,
                QueueEnum::MAIN_EXCHANGE,
                $messageDto->getRoutingKey(),
            )
        ;
    }

    public function get(
        string $queueName,
    ): ?AMQPMessage
    {
        return $this->connection->channel()
            ->basic_get(
                $queueName,
                 true
            )
        ;
    }

    public function repeat(
        AMQPMessage $message,
        string $queueName,
    ): void
    {
        $this->connection->channel()
            ->basic_publish(
                $message,
                QueueEnum::MAIN_EXCHANGE,
                $queueName
            )
        ;
    }
}
