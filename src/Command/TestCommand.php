<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\NanValueException;
use App\Exception\PropertyNotFoundException;
use App\Exception\ImpossibleDiscriminantValueException;
use App\Exception\SeniorCoefficientException;
use Exception;

class TestCommand implements CommandInterface
{
    private ?int $exceptionType;

    public function __construct(?int $exceptionType = null)
    {
        $this->exceptionType = $exceptionType;
    }

    /**
     * @throws ImpossibleDiscriminantValueException
     * @throws NanValueException
     * @throws PropertyNotFoundException
     * @throws SeniorCoefficientException
     * @throws Exception
     */
    public function execute(): void
    {
        $this->test();
    }

    /**
     * @throws ImpossibleDiscriminantValueException
     * @throws NanValueException
     * @throws PropertyNotFoundException
     * @throws SeniorCoefficientException
     * @throws Exception
     */
    private function test(): void
    {
        if ($this->exceptionType === null) {
            echo 'I am valid!';

            return;
        }

        match ($this->exceptionType) {
            1 => throw new NanValueException(),
            2 => throw new PropertyNotFoundException(),
            3 => throw new ImpossibleDiscriminantValueException(),
            4 => throw new SeniorCoefficientException(),
            default => throw new Exception(),
        };
    }
}
