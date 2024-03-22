<?php

namespace App\ProjectCascade\Dto;

use SensitiveParameter;

class ProtectedDto
{
    use DtoResolverTrait {
        DtoResolverTrait::__construct as private __dtoConstruct;
    }

    public function __construct(
        #[SensitiveParameter]
        array $data = []
    ) {
        $this->__dtoConstruct($data);
    }

    private array $data;

    public function getData(): array
    {
        return $this->data;
    }
}
