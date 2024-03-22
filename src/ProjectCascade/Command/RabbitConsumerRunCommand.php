<?php

namespace App\ProjectCascade\Command;

use App\Command\CommandInterface;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeConsumer;
use App\ProjectCascade\RabbitMQ\RabbitClient;
use JsonException;

class RabbitConsumerRunCommand implements CommandInterface
{
    private RabbitClient $rabbitClient;
    private CascadeConsumer $consumer;

    public function __construct(private readonly string $queueName)
    {
        $this->rabbitClient = new RabbitClient();
        $this->consumer = new CascadeConsumer();
    }

    /**
     * @throws JsonException
     */
    public function execute(): void
    {
        $messageList = [];

        do {
            $message = $this->rabbitClient->get($this->queueName);
            $messageList[] = $message;
        } while ($message !== null);

        $this->consumer->process(array_filter($messageList));

        sleep(1);
        $this->execute();
    }
}
