<?php

declare(strict_types=1);

namespace App\ProjectCascade\RabbitMQ;

interface RabbitHandlerInterface
{
    public function handle(RabbitDtoInterface $dto);
}
