<?php

declare(strict_types=1);

namespace App\ProjectCascade\UseCase\AuthHandler;

use App\ProjectCascade\DBMaintence\DBManager;
use App\ProjectCascade\Exception\PlayerNotFoundException;
use App\ProjectCascade\Service\IoCResolverService;
use Doctrine\DBAL\Exception;
use GuzzleHttp\Psr7\Request;

readonly class AuthHandler implements AuthInterface
{
    /** @var AuthManager  */
    private DBManager $manager;

    public function __construct()
    {
        $this->manager = IoCResolverService::getManager(self::class);
    }

    /**
     * @throws PlayerNotFoundException
     * @throws Exception
     */
    public function auth(Request $request): string
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
