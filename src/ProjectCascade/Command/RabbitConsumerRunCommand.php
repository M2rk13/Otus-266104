<?php

declare(strict_types=1);

namespace App\ProjectCascade\Command;

use App\Command\CommandInterface;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeConsumer;
use App\ProjectCascade\RabbitMQ\RabbitClientInterface;
use App\ProjectCascade\Service\IoCResolverService;
use JsonException;

class RabbitConsumerRunCommand implements CommandInterface
{
    private RabbitClientInterface $rabbitClient;
    private CascadeConsumer $consumer;

    public function __construct(private readonly string $queueName)
    {
        /** @var RabbitClientInterface $rabbitClient */
        $rabbitClient = IoCResolverService::getClass(RabbitClientInterface::class);
        $this->rabbitClient = $rabbitClient;

        /** @var CascadeConsumer $consumer */
        $consumer = IoCResolverService::getClass(CascadeConsumer::class);
        $this->consumer = $consumer;
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
