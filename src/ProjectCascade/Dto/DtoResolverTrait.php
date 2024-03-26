<?php

declare(strict_types=1);

namespace App\ProjectCascade\Dto;

use function property_exists;

trait DtoResolverTrait
{
    public function __construct(array $data = [])
    {
        foreach ($data as $propertyName => $value) {
            if (property_exists($this, $propertyName)) {
                $this->$propertyName = $value;
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
