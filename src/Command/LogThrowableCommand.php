<?php

declare(strict_types=1);

namespace App\Command;

use App\Resources\ExceptionHandler\ExceptionHandler;
use Throwable;

class LogThrowableCommand implements CommandInterface
{
    private readonly Throwable $exception;
    private readonly ExceptionHandler $logger;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
        $this->logger = new ExceptionHandler();
    }

    public function execute(): void
    {
        $this->logger->logException($this->exception);
    }
}
