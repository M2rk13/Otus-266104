<?php

namespace App\ProjectCascade\RabbitMQ;

interface RabbitHandlerInterface
{
    public function handle(RabbitDtoInterface $dto);
}
