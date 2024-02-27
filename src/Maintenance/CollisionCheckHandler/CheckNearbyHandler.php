<?php

declare(strict_types=1);

namespace App\Maintenance\CollisionCheckHandler;

use App\Enum\SpaceObjectPropertyEnum;
use App\Exception\PropertyNotFoundException;
use App\Maintenance\Dto\GameFieldChunkDto;
use App\Maintenance\Dto\GameFieldChunkListDto;

class CheckNearbyHandler extends AbstractGameObjectHandler
{
    /**
     * @throws PropertyNotFoundException
     */
    public function next(GameFieldChunkListDto $gameFieldListDto): ?GameObjectHandlerInterface
    {
        if (empty($gameFieldListDto->gameFieldListDto)) {
            return null;
        }

        $idGameField = 0;
        $gameObjectGroupByNearby = [];

        foreach ($gameFieldListDto->gameFieldListDto as $gameFieldDto) {
            $idGameField = $gameFieldDto->id;
            $gameObjectGroupByNearbyByField = [];

            foreach ($gameFieldDto->gameObjectList as $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);
                $name = $gameObject->getProperty(SpaceObjectPropertyEnum::SPACE_OBJECT_NAME);

                $gameObjectGroupByNearbyByField[$position][] = $name;
            }

            $gameObjectGroupByNearby[$idGameField] = $gameObjectGroupByNearbyByField;
        }

        $newGameField = new GameFieldChunkDto(++$idGameField, []);

        foreach ($gameFieldListDto->gameFieldListDto as $gameFieldDto) {
            $gameFieldId = $gameFieldDto->id;

            foreach ($gameFieldDto->gameObjectList as $key => $gameObject) {
                $position = $gameObject->getProperty(SpaceObjectPropertyEnum::SIMPLE_POSITION);

                if (
                    isset($gameObjectGroupByNearby[$gameFieldId][$position])
                    && count($gameObjectGroupByNearby[$gameFieldId][$position]) > 1
                ) {
                    array_shift($gameObjectGroupByNearby[$gameFieldId][$position]);
                    $newGameField->gameObjectList[] = $gameObject;
                    unset($gameFieldDto->gameObjectList[$key]);
                }
            }
        }

        if (!empty($newGameField->gameObjectList)) {
            $gameFieldListDto->gameFieldListDto[] = $newGameField;
        }

        return parent::next($gameFieldListDto);
    }
}
