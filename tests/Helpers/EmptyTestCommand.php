<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Command\CommandInterface;

class EmptyTestCommand implements CommandInterface
{
    public function execute(): void {}
}
