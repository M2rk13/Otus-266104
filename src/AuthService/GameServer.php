<?php

declare(strict_types=1);

namespace App\AuthService;

use App\Exception\AuthException;
use JsonException;

class GameServer
{
    private string $groupId;
    private string $key = 'some_key';
    private array $playerIdList = [];
    private AuthService $authService;

    public function __construct(
    ) {
        $this->authService = new AuthService();
    }

    public function register(array $playerIdList): string
    {
        $this->playerIdList = $playerIdList;
        $this->groupId = $this->authService->authGroup($playerIdList);

        return $this->groupId;
    }

    public function login(string $playerId): string
    {
        return $this->authService->getAuthKey(
            [
                'groupId' => $this->groupId,
                'playerId' => $playerId
            ],
            $this->key
        );
    }

    /**
     * @throws JsonException
     */
    public function checkAuthorization(string $jwt): void
    {
        $decodedJwt = $this->authService->decodeJwt($jwt, $this->key);
        $playerId = $decodedJwt['data']['playerId'];

        if (in_array($playerId, $this->playerIdList, true) === false) {
            throw new AuthException('permission not granted');
        }
    }
}
