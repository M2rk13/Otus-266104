<?php

declare(strict_types=1);

namespace App\ProjectCascade\Exception;

class DBException extends ProjectCascadeException
{
    private const MESSAGE = 'DataBase error occurred';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
