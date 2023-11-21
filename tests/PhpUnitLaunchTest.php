<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class PhpUnitLaunchTest extends TestCase
{
    private const ANY_TEST_VALUE = 1;

    public function testIncrementPoolNestedLevel(): void
    {
        self::assertSame(self::ANY_TEST_VALUE, self::ANY_TEST_VALUE);
    }
}
