<?php

namespace App\RabbitMq;

class RabbitMessageParameter
{
    public function __construct(
        public string $type,
        public mixed $value,
    ){
    }
}
