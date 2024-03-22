<?php

namespace App\ProjectCascade\Exception;

class TransactionNotFoundException extends ProjectCascadeException
{
    private const MESSAGE = 'transaction details not available';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
