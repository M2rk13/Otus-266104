<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use function getmypid;
use function hexdec;
use function str_pad;
use function substr;
use function uniqid;

final class IdGenerator
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

    public static function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
