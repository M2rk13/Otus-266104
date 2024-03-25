<?php

declare(strict_types=1);

namespace App\ProjectCascade\UseCase\AuthHandler;

use App\ProjectCascade\DBMaintence\DBManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;

class AuthManager extends DBManager
{
    /**
     * @throws Exception
     */
    public function findPlayerIdByToken(string $token): ?string
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('pt.playerId')
            ->from('PlayerToken', 'pt')
            ->where('pt.playerToken = :playerToken')
            ->andWhere('pt.active = :activeTrue')
            ->setParameters(
                [
                    'playerToken' => $token,
                    'activeTrue' => true,
                ],
                [
                    'activeTrue' => ParameterType::BOOLEAN
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return (string) $stmt->fetchOne() ?: null;
    }
}
