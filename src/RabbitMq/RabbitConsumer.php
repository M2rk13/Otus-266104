<?php

declare(strict_types=1);

namespace App\RabbitMq;

use App\Command\InterpretCommand;
use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;
use Exception;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitConsumer
{
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly IoC $ioC,
        private readonly AMQPChannel $channel,
        private readonly string $queueName,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function putMessage(
        string $scopeId,
        string $objectId,
        string $commandClass,
        array $parameterList = []
    ): void {
        $msg = new RabbitMessage(
            $scopeId,
            $objectId,
            $commandClass,
            $parameterList
        );

        $queue = new RabbitQueue($this->channel, $this->queueName);

        $queue->sendMessage($msg);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function processNextMessage(): void
    {
        $queue = new RabbitQueue($this->channel, $this->queueName);

        $msg = $queue->extractNextMessage();

        if ($msg === null) {
            return;
        }

        $this->ioC->resolve(IoC::SCOPES_CURRENT, $msg->scopeId);
        $command = new InterpretCommand($this->ioC, $msg);

        /** @var Queue $commandQueue */
        $commandQueue = $this->ioC->resolve(Queue::class);
        $commandQueue->push($command);
    }
}
