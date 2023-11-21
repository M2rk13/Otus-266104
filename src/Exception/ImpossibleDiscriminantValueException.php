<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class ImpossibleDiscriminantValueException extends MathematicalException
{
    private const MESSAGE = 'this value of discriminant is not possible';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
