<?php

namespace App\ProjectCascade\Exception;

class TransactionNotProcessableException extends ProjectCascadeException
{
    private const MESSAGE = 'transaction is not processable';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
