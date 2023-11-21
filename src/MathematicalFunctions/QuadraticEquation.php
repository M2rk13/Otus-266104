<?php

declare(strict_types=1);

namespace App\MathematicalFunctions;

use App\Exception\ImpossibleDiscriminantValueException;
use App\Exception\NanValueException;
use App\Exception\SeniorCoefficientException;

final class QuadraticEquation
{
    private const DEFAULT_FLOAT_SCALE = 16;

    /**
     * @throws ImpossibleDiscriminantValueException
     * @throws NanValueException
     * @throws SeniorCoefficientException
     */
    public static function execute(float $a, float $b = 0, float $c = 0): array
    {
        if (is_nan($a) || is_nan($b) || is_nan($c)) {
            throw new NanValueException();
        }

        bcscale(self::DEFAULT_FLOAT_SCALE);
        $aStr = self::getNumberFormatted($a);
        $bStr = self::getNumberFormatted($b);
        $cStr = self::getNumberFormatted($c);

        if (bccomp($aStr, '0') === 0) {
            throw new SeniorCoefficientException('value can\'t be equal 0');
        }

        $disc = bcsub(
            bcmul($bStr, $bStr),
            bcmul('4', bcmul($aStr, $cStr))
        );

        return match(bccomp($disc, '0')) {
            -1 => [],
            0 => [
                (float) bcdiv($bStr, bcmul('2', $aStr)),
            ],
            1 => [
                (float) bcdiv(
                    bcsub($bStr, bcsqrt($disc)),
                    bcmul('2', $aStr)
                ),
                (float) bcdiv(
                    bcadd($bStr, bcsqrt($disc)),
                    bcmul('2', $aStr)
                ),
            ],
            default => throw new ImpossibleDiscriminantValueException($disc),
        };
    }

    private static function getNumberFormatted(float $value): string
    {
         $integerValuePart = number_format($value);
         $integerLengthPart = strlen($integerValuePart);

        return number_format($value, self::DEFAULT_FLOAT_SCALE - $integerLengthPart, '.', '');
    }
}
