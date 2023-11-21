<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class SeniorCoefficientException extends MathematicalException
{
    private const MESSAGE = 'an error occurred with senior coefficient';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
