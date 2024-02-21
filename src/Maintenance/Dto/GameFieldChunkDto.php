<?php

declare(strict_types=1);

namespace App\Maintenance\Dto;

use App\SpaceObjects\SpaceObject;

class GameFieldChunkDto
{
    /**
     * @param SpaceObject[] $gameObjectList
     */
    public function __construct(
        public int $id,
        public array $gameObjectList
    ) {
    }
}
