<?php

declare(strict_types=1);

namespace App\Tests\MathematicalFunctionsTests;

use App\Exception\ImpossibleDiscriminantValueException;
use App\Exception\NanValueException;
use App\Exception\SeniorCoefficientException;
use App\MathematicalFunctions\QuadraticEquation;
use Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

use function PHPUnit\Framework\assertSame;

class QuadraticEquationTest extends TestCase
{
    /**
     * @dataProvider providerQuadraticEquationValidTest
     *
     * @throws ImpossibleDiscriminantValueException
     * @throws NanValueException
     * @throws SeniorCoefficientException
     */
    public function testQuadraticEquationValid(
        array $parameterList,
        array $expectedValue,
    ): void {
        assertSame($expectedValue, QuadraticEquation::execute(...$parameterList));
    }

    public static function providerQuadraticEquationValidTest(): array
    {
        return [
            'noRoots' => [
                'parameterList' => [
                    'a' => 1.0,
                    'b' => 0.0,
                    'c' => 1.0,
                ],
                'expectedValue' => [],
            ],
            'twoRoots' => [
                'parameterList' => [
                    'a' => 1.0,
                    'b' => 0.0,
                    'c' => -1.0,
                ],
                'expectedValue' => [
                    -1.0,
                    1.0,
                ],
            ],
            'oneRoot' => [
                'parameterList' => [
                    'a' => 1.0,
                    'b' => -2.0,
                    'c' => 1.0,
                ],
                'expectedValue' => [
                    -1.0,
                ],
            ],
            'oneRootEpsilonDisc' => [
                'parameterList' => [
                    'a' => 1 + PHP_FLOAT_EPSILON,
                    'b' => -2 + PHP_FLOAT_EPSILON,
                    'c' => 1 + PHP_FLOAT_EPSILON,
                ],
                'expectedValue' => [
                    -1.0,
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerQuadraticEquationErrorTest
     *
     * @throws ImpossibleDiscriminantValueException
     * @throws NanValueException
     * @throws SeniorCoefficientException
     */
    public function testQuadraticEquationError(
        array $parameterList,
        string $expectedException,
    ): void {
        $this->expectException($expectedException);
        QuadraticEquation::execute(...$parameterList);
    }

    public static function providerQuadraticEquationErrorTest(): array
    {
        return [
            'seniorCoefficientZero' => [
                'parameterList' => [
                    'a' => 0.0,
                    'b' => 2.0,
                    'c' => 1.0,
                ],
                'expectedException' => SeniorCoefficientException::class,
            ],
            'doubleValues' => [
                'parameterList' => [
                    'a' => PHP_FLOAT_EPSILON,
                    'b' => -2.0,
                    'c' => 1.0,
                ],
                'expectedException' => SeniorCoefficientException::class,
            ]
        ];
    }

    /**
     * @dataProvider providerWrongParameterTypes
     *
     *
     * @throws ImpossibleDiscriminantValueException
     * @throws NanValueException
     * @throws SeniorCoefficientException
     */
    public function testWrongParameterTypes(
        $parameter,
    ): void {
        $this->expectException(TypeError::class);
        QuadraticEquation::execute($parameter, 1.0, 2.0);

        $this->expectException(TypeError::class);
        QuadraticEquation::execute(1.0, $parameter, 2.0);

        $this->expectException(TypeError::class);
        QuadraticEquation::execute(2.0, 1.0, $parameter);
    }

    public static function providerWrongParameterTypes(): array
    {
        return [
            'string' => [
                'parameter' => '0.0',
            ],
            'boolean' => [
                'parameter' => false,
            ],
            'array' => [
                'parameter' => [6.7],
            ],
            'object' => [
                'parameter' => new Exception(),
            ],
            'null' => [
                'parameter' => null,
            ],
        ];
    }

    /**
     * @throws ImpossibleDiscriminantValueException
     * @throws SeniorCoefficientException
     */
    public function testNotANumberParameterTypes(): void
    {
        $parameter = NAN;

        $this->expectException(NanValueException::class);
        QuadraticEquation::execute($parameter, 1.0, 2.0);

        $this->expectException(NanValueException::class);
        QuadraticEquation::execute(1.0, $parameter, 2.0);

        $this->expectException(NanValueException::class);
        QuadraticEquation::execute(2.0, 1.0, $parameter);
    }

}
