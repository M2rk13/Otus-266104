<?php

declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class PropertyNotFoundException extends SpaceShipGameException
{
    private const MESSAGE = 'property was not found';

    public function __construct(string $message = '')
    {
        parent::__construct(sprintf('%s: %s', self::MESSAGE, $message));
    }
}
