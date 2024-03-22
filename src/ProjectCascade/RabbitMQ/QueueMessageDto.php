<?php

namespace App\ProjectCascade\RabbitMQ;

use App\ProjectCascade\Dto\DtoResolverTrait;

class QueueMessageDto
{
    use DtoResolverTrait;

    private RabbitDtoInterface $dto;
    private string $className;
    private string $routingKey;

    public function getDto(): RabbitDtoInterface
    {
        return $this->dto;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }
}
