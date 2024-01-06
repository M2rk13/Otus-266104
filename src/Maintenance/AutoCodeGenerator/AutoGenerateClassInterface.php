<?php

declare(strict_types=1);

namespace App\Maintenance\AutoCodeGenerator;

interface AutoGenerateClassInterface
{
    public function getGeneratedClassBody(): string;
}
