<?php

declare(strict_types=1);

namespace App\RabbitMq;

class RabbitMessage
{
    public function __construct(
        public readonly string $scopeId,
        public readonly string $objectId,
        public readonly string $commandClass,
        /** @var RabbitMessageParameter $parameterList */
        public readonly array $parameterList = [],
    ) {
    }
}
