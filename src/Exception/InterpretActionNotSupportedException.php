<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class InterpretActionNotSupportedException extends MathematicalException
{
    private const MESSAGE = 'interpret action not supported';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
