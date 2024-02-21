<?php

declare(strict_types=1);

namespace App\Maintenance\CollisionCheckHandler;

use App\Command\CommandInterface;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\Dto\GameFieldChunkListDto;

class CheckCollisionCommand implements CommandInterface
{
    private CheckNearbyHandler $checkNearbyHandler;

    public function __construct(
        private readonly GameFieldChunkListDto $gameFieldListDto,
    ) {
        $this->checkNearbyHandler = new CheckNearbyHandler();
        $this->checkNearbyHandler->setNext(new CheckCollisionHandler());
    }

    /**
     * @throws PropertyNotFoundException
     */
    public function execute(): void
    {
        do {
            $result = $this->checkNearbyHandler->next($this->gameFieldListDto);
        } while ($result !== null);
    }
}
