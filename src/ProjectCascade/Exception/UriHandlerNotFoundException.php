<?php

namespace App\ProjectCascade\Exception;

class UriHandlerNotFoundException extends ProjectCascadeException
{
    private const MESSAGE = 'uri handler not found';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
