<?php

declare(strict_types=1);

namespace App\Maintenance\Dto;

class GameFieldChunkListDto
{
    /**
     * @param GameFieldChunkDto[] $gameFieldListDto
     */
    public function __construct(public array $gameFieldListDto)
    {
    }
}
