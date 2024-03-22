<?php

namespace App\ProjectCascade\UseCase\AuthHandler;

use App\ProjectCascade\Exception\PlayerNotFoundException;
use Doctrine\DBAL\Exception;
use GuzzleHttp\Psr7\Request;

class AuthHandler
{
    private readonly AuthManager $manager;

    public function __construct()
    {
        $this->manager = new AuthManager();
    }

    /**
     * @throws PlayerNotFoundException
     * @throws Exception
     */
    public function getPlayerId(Request $request): string
    {
        $authHeader = $request->getHeader('auth');
        $authToken = array_shift($authHeader);

        $playerId = $this->manager->findPlayerIdByToken($authToken);

        if (empty($playerId)) {
            throw new PlayerNotFoundException();
        }

        return $playerId;
    }
}
