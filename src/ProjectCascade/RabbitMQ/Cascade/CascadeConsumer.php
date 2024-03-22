<?php

declare(strict_types=1);

namespace App\ProjectCascade\RabbitMQ\Cascade;

use App\ProjectCascade\RabbitMQ\AbstractConsumer;
use App\ProjectCascade\RabbitMQ\RabbitHandlerInterface;

class CascadeConsumer extends AbstractConsumer
{
    protected readonly RabbitHandlerInterface $handler;

    public function __construct()
    {
        $this->handler = new CascadeHandler();

        parent::__construct();
    }
}
