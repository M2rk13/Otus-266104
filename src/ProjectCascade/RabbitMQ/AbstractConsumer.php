<?php

namespace App\ProjectCascade\RabbitMQ;

use JsonException;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

abstract class AbstractConsumer
{
    protected readonly RabbitHandlerInterface $handler;
    private RabbitClient $rabbitClient;

    public function __construct()
    {
        $this->rabbitClient = new RabbitClient();
    }

    /**
     * @param AMQPMessage[] $messageList
     * @throws JsonException
     */
    public function process(array $messageList): void
    {
        foreach ($messageList as $message) {
            $dto = $this->decodeMessageBody($message);

            try {
                $this->handler->handle($dto->getDto());
            } catch (Throwable $e) {
                // log exception

                $this->rabbitClient->repeat($message, $dto->getRoutingKey());
            }
        }
    }

    /**
     * @throws JsonException
     */
    protected function decodeMessageBody(AMQPMessage $message): QueueMessageDto
    {
        $body = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $className = $body['className'];
        $dto = new $className($body['data']);
        $routingKey = $body['routingKey'];

        return new QueueMessageDto([
            'dto' => $dto,
            'className' => $className,
            'routingKey' => $routingKey
        ]);
    }
}
