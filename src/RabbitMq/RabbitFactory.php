<?php

namespace App\RabbitMq;

use App\Exception\ProjectException;
use App\Maintenance\Ioc\IoC;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitFactory
{
    public function __construct(
        private readonly IoC $ioC,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getConsumer(string $queueName): RabbitConsumer
    {
        $channel = $this->getChannel();

        return new RabbitConsumer($this->ioC, $channel, $queueName);
    }

    protected function getChannel(): AMQPChannel
    {
        try {
            return $this->ioC->resolve(AMQPChannel::class);
        } catch (ProjectException) {
            $this->ioC->resolve(IoC::IOC_REGISTER, AMQPChannel::class, function () {
                $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'rmuser', 'rmpassword');

                return $connection->channel();
            });
        }

        return $this->ioC->resolve(AMQPChannel::class);
    }
}
