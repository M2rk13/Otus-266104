<?php

declare(strict_types=1);

namespace App\ProjectCascade\RabbitMQ\Cascade;

use App\ProjectCascade\RabbitMQ\AbstractConsumer;
use App\ProjectCascade\RabbitMQ\RabbitHandlerInterface;
use App\ProjectCascade\Service\IoCResolverService;

class CascadeConsumer extends AbstractConsumer
{
    protected readonly RabbitHandlerInterface $handler;

    public function __construct()
    {
        /** @var CascadeHandler $handler */
        $handler = IoCResolverService::getClass(CascadeHandler::class);
        $this->handler = $handler;

        parent::__construct();
    }
}
