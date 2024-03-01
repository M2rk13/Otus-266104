<?php

declare(strict_types=1);

namespace App\Maintenance\Interpreter;

use App\Command\InterpretFireCommand;
use App\Command\InterpretStartCommand;
use App\Command\InterpretStopCommand;
use App\Enum\SpaceGameActionEnum;
use App\Exception\InterpretActionNotSupportedException;

class InterpretActionRegistry
{
    // хардкод это условность, по сути этот реестр можно организовать разными способами автозагрузки,
    // но в рамках задачи роли не играет как мы его получили
    /** @var InterpretCommandInterface[] */
    private array $actionRegistry = [
        SpaceGameActionEnum::ACTION_STOP_MOVE => InterpretStopCommand::class,
        SpaceGameActionEnum::ACTION_START_MOVE => InterpretStartCommand::class,
        SpaceGameActionEnum::ACTION_FIRE => InterpretFireCommand::class,
    ];

    /**
     * @throws InterpretActionNotSupportedException
     */
    public function getCommand(string $key): string
    {
        $name = $this->actionRegistry[$key] ?? null;

        if (empty($name)) {
            throw new InterpretActionNotSupportedException($key);
        }

        return $name;
    }
}
