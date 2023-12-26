<?php

declare(strict_types=1);

namespace App\Resources\ExceptionHandler;

use Throwable;

interface LogExceptionInterface
{
    public function logException(Throwable $e);
}
