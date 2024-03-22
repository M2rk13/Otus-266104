<?php

namespace App\ProjectCascade\RabbitMQ\Cascade;

use App\ProjectCascade\DBMaintence\DBManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;

class CascadeManager extends DBManager
{
    /**
     * @throws Exception
     */
    public function getProviderProcessedListId(string $primalTransaction): array
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('p.name')
            ->from('TransactionCascade', 'tc')
            ->innerJoin('tc', 'Transaction', 't', 't.id = tc.currentTransactionId')
            ->innerJoin('t', 'Provider', 'p', 'p.id = t.providerId')
            ->where('tc.primalTransactionId = :primalTransactionId')
            ->setParameters(
                [
                    'primalTransactionId' => $primalTransaction,
                ],
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchFirstColumn();
    }

    /**
     * @throws Exception
     */
    public function getProviderListToProcess(array $processedProviderList): array
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('p.name')
            ->from('Provider', 'p')
            ->where('p.name <> :cascadeProvider')
            ->andWhere('p.active = :activeTrue')
            ->andWhere('p.id NOT IN (:providerIdList)')
            ->setParameters(
                [
                    'cascadeProvider' => 'cascade',
                    'activeTrue' => true,
                    'providerIdList' => implode(',', $processedProviderList),
                ],
                [
                    'activeTrue' => Types::BOOLEAN,
                    'providerIdList' => Types::SIMPLE_ARRAY
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchFirstColumn();
    }
}
