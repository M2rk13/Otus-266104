<?php

namespace App\ProjectCascade\Exception;

class PaymentProviderException extends ProjectCascadeException
{
    private const MESSAGE = 'an error occurred during payment request to provider for transaction: [%s]';

    public function __construct(string $transactionId)
    {
        parent::__construct(sprintf(self::MESSAGE, $transactionId));
    }
}
