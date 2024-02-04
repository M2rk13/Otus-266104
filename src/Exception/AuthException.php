<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class AuthException extends ProjectException
{
    private const MESSAGE = 'authentication error';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
