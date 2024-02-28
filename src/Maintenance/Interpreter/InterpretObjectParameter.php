<?php

declare(strict_types=1);

namespace App\Maintenance\Interpreter;

class InterpretObjectParameter
{
    public function __construct(
        public string $type,
        public mixed $value,
    ){
    }
}
