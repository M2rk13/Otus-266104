<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class NotEnoughFuelException extends SpaceShipGameException
{
    private const MESSAGE = 'not enough fuel';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
