<?php

declare(strict_types=1);

namespace App\AuthService;

use App\Exception\AuthException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use JsonException;

class AuthService
{
    private const ALGORITHM = 'HS256';
    private const SERVER_NAME = 'SpaceShipGameServer';

    private array $playerGroupList = [];

    public function authGroup(array $playerIdList): string
    {
        $groupId = $this->getGroupId(count($this->playerGroupList));
        $this->playerGroupList[$groupId] = $playerIdList;

        return $groupId;
    }

    /**
     * @throws AuthException
     */
    public function getAuthKey(array $data, string $key): string
    {
        $groupId = $data['groupId'];
        $playerId = $data['playerId'];

        $playerGroup = $this->playerGroupList[$groupId] ?? null;

        if ($playerGroup === null) {
            throw new AuthException(sprintf('there is no PlayerGroup with id [%s]', $groupId));
        }

        if (in_array($playerId, $playerGroup, true) === false) {
            throw new AuthException(sprintf(
                'there is no Player with ID [%s] in PlayerGroup with ID [%s]',
                $playerId,
                $groupId
            ));
        }

        $payload = [
            'timestamp' => time(),
            'server' => self::SERVER_NAME,
            'data' => $data,
        ];

        return JWT::encode($payload, $key, self::ALGORITHM);
    }

    /**
     * @throws JsonException
     */
    public function decodeJwt(string $jwt, string $key): array
    {
        $decodedJwt = JWT::decode($jwt, new Key($key, self::ALGORITHM));

        return json_decode(json_encode($decodedJwt, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    private function getGroupId(int $groupCount): string
    {
        return md5(self::SERVER_NAME . $groupCount);
    }
}
