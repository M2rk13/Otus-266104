<?php

namespace App\Queue;

class Queue
{
    private array $collection = [];

    public function push(...$valueList): void
    {
        foreach ($valueList as $value) {
            $this->collection[] = $value;
        }
    }

    public function extract(): mixed
    {
        return array_shift($this->collection);
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
