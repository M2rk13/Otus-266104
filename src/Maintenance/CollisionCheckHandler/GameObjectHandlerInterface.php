<?php

declare(strict_types=1);

namespace App\Maintenance\CollisionCheckHandler;

use App\Maintenance\Dto\GameFieldChunkListDto;

interface GameObjectHandlerInterface
{
    public function setNext(GameObjectHandlerInterface $handler): GameObjectHandlerInterface;

    public function next(GameFieldChunkListDto $gameFieldListDto): ?GameObjectHandlerInterface;
}
