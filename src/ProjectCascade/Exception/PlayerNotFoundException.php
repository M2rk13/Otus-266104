<?php

namespace App\ProjectCascade\Exception;

class PlayerNotFoundException extends ProjectCascadeException
{
    private const MESSAGE = 'player not found';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
