<?php

namespace App\ProjectCascade\RabbitMQ\Cascade;

use App\ProjectCascade\Dto\DtoResolverTrait;
use App\ProjectCascade\RabbitMQ\RabbitDtoInterface;

class CascadeMessageDto implements RabbitDtoInterface
{
    use DtoResolverTrait;
    private string $transactionId;

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}
