<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Command\LogThrowableCommand;
use App\Command\MultipleRepeatCommand;
use App\Command\RepeaterCommand;
use App\Command\TestCommand;
use App\Exception\NanValueException;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class TryCatchBasicTest extends TestCase
{
    public function testLogException(): void
    {
        $command = new TestCommand(100);

        $file = fopen(sys_get_temp_dir() . '/analog.txt', 'wb');
        fclose($file);
        $logFileBefore = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        try {
            $command->execute();
        } catch (Exception $e) {
            $logCommand = new LogThrowableCommand($e);
            $logCommand->execute();
        }

        $logFileAfter = file_get_contents(sys_get_temp_dir() . '/analog.txt');
        self::assertNotSame($logFileBefore, $logFileAfter);
    }

    public function testRepeatCommand(): void
    {
        $command = new TestCommand(1);

        try {
            $command->execute();
        } catch (Exception) {
            $repeater = new RepeaterCommand($command);
            $this->expectException(NanValueException::class);
            $repeater->execute();
        }
    }

    public function testLogAfterRepeat(): void
    {
        $command = new TestCommand(1);
        $logFileBefore = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        try {
            $command->execute();
        } catch (Exception) {
            try {
                $repeater = new RepeaterCommand($command);
                $repeater->execute();
            } catch (Exception $e) {
                $logCommand = new LogThrowableCommand($e);
                $logCommand->execute();
            }
        }

        $logFileAfter = file_get_contents(sys_get_temp_dir() . '/analog.txt');
        self::assertNotSame($logFileBefore, $logFileAfter);
    }

    public function testLogAfterMultipleRepeat(): void
    {
        $command = new TestCommand(1);
        $logFileBefore = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        try {
            $command->execute();
        } catch (Exception) {
            $repeater = new MultipleRepeatCommand($command);
            $repeater->execute();
        }

        $logFileAfter = file_get_contents(sys_get_temp_dir() . '/analog.txt');

        $reflection = new ReflectionObject($repeater);
        $property = $reflection->getProperty('counter');
        $counter = $property->getValue($repeater);

        self::assertNotSame($logFileBefore, $logFileAfter);
        self::assertSame($counter, 2);
    }
}