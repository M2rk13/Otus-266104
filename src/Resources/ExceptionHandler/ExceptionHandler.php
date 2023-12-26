<?php

declare(strict_types=1);

namespace App\Resources\ExceptionHandler;

use Analog\Logger;
use Exception;
use Psr\Log\LogLevel;
use Throwable;

class ExceptionHandler implements LogExceptionInterface
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function logException(Throwable $e): void
    {
        $logLevel = $e instanceof Exception ? LogLevel::ERROR : LogLevel::CRITICAL;
        $path = implode(" -> \n", array_column($e->getTrace(), 'file'));
        $message = sprintf('Error [%s] occurred in [%s]', $e->getMessage(), $path);

        $this->logger->log($logLevel, $message, [
            'exception' => $e,
            'path' => $path,
        ]);
    }
}
