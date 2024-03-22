<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use function getmypid;
use function hexdec;
use function str_pad;
use function substr;
use function uniqid;

class IdGenerator
{
    /**
     * @return string
     */
    public static function generateUniqueId(): string
    {
        $hexDecSting = (string) hexdec(uniqid('', false));
        $pidString = (string) getmypid();

        return ltrim(
            str_pad(
                    substr($pidString, -5), 5, '0'
                ) . substr($hexDecSting, -13),
            '0'
        );
    }
}
