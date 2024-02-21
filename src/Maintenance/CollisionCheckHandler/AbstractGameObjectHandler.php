<?php

declare(strict_types=1);

namespace App\Maintenance\CollisionCheckHandler;

use App\Maintenance\Dto\GameFieldChunkListDto;

abstract class AbstractGameObjectHandler implements GameObjectHandlerInterface
{
    private ?GameObjectHandlerInterface $nextHandler = null;

    public function setNext(GameObjectHandlerInterface $handler): GameObjectHandlerInterface
    {
        $this->nextHandler = $handler;

        return $this->nextHandler;
    }

    public function next(GameFieldChunkListDto $gameFieldListDto): ?GameObjectHandlerInterface
    {
        return $this->nextHandler?->next($gameFieldListDto);
    }
}
