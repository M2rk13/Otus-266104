<?php

declare(strict_types=1);

namespace App\Command;

class RepeaterCommand implements CommandInterface
{
    private CommandInterface $command;

    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    public function execute(): void
    {
        $this->command->execute();
    }
}
