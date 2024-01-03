<?php

declare(strict_types=1);

namespace App\Command;

final class MacroCommand implements CommandInterface
{
    /**
     * @param CommandInterface[] $commandList
     */
    public function __construct(
        private readonly array $commandList
    ) {
    }

    public function execute(): void
    {
        foreach ($this->commandList as $command) {
            $command->execute();
        }
    }
}
