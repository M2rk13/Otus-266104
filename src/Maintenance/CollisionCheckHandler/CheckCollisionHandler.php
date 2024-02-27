<?php

declare(strict_types=1);

namespace App\Maintenance\CollisionCheckHandler;

use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\Dto\GameFieldChunkListDto;

class CheckCollisionHandler extends AbstractGameObjectHandler
{
    /**
     * @throws PropertyNotFoundException
     */
    public function next(GameFieldChunkListDto $gameFieldListDto): ?GameObjectHandlerInterface
    {
        $gameObjectGroupByNearby = [];

        foreach ($gameFieldListDto->gameFieldListDto as $gameFieldDto) {
            $gameObjectGroupByNearbyByField = [];

            foreach ($gameFieldDto->gameObjectList as $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);
                $name = $gameObject->getProperty(SpaceObjectPropertyEnum::SPACE_OBJECT_NAME);

                $gameObjectGroupByNearbyByField[$position][] = $name;
            }

            $gameObjectGroupByNearby[$gameFieldDto->id] = $gameObjectGroupByNearbyByField;
        }

        foreach ($gameFieldListDto->gameFieldListDto as $gameFieldDto) {
            foreach ($gameFieldDto->gameObjectList as $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);

                if (
                    isset($gameObjectGroupByNearby[$gameFieldDto->id][$position])
                    && count($gameObjectGroupByNearby[$gameFieldDto->id][$position]) > 1
                ) {
                    return $this;
                }
            }
        }

        return parent::next($gameFieldListDto);
    }
}
