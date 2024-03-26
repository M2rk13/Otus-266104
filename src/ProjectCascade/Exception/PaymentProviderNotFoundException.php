<?php

declare(strict_types=1);

namespace App\ProjectCascade\Exception;

class PaymentProviderNotFoundException extends ProjectCascadeException
{
    private const MESSAGE = 'provider not found';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
