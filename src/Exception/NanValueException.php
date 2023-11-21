<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class NanValueException extends MathematicalException
{
    private const MESSAGE = 'received "not a number" float value';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
