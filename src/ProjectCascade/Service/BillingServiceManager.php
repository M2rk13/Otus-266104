<?php

namespace App\ProjectCascade\Service;

use App\ProjectCascade\DBMaintence\DBManager;
use App\ProjectCascade\Dto\PaymentDto;
use App\ProjectCascade\Dto\ProtectedDto;
use App\ProjectCascade\Enum\CascadeTransactionStatusEnum;
use App\ProjectCascade\Enum\TransactionStatusEnum;
use App\ProjectCascade\Exception\DBException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;

class BillingServiceManager extends DBManager
{
    /**
     * @throws Exception
     */
    public function findProviderId(string $getProviderName): ?string
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('p.id')
            ->from('Provider', 'p')
            ->where('p.name = :providerName')
            ->andWhere('p.active = :activeTrue')
            ->setParameters(
                [
                    'providerName' => $getProviderName,
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

        return $stmt->fetchOne() ?: null;
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function createTransaction(PaymentDto $paymentDto, string $providerId): string
    {
        return $this->connection->insert(
            'Transaction',
            [
                'playerId' => $paymentDto->getPlayerId(),
                'providerId' => $providerId,
                'amount' => $paymentDto->getAmount(),
                'status' => TransactionStatusEnum::NEW,
            ],
        );
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function insertTransactionDetailsKey(ProtectedDto $protectedDto, string $transactionId): void
    {
        $this->connection->insert(
            'TransactionDetailsKey',
            [
                'transactionId' => $transactionId,
                'decryptKey' => $protectedDto->getData()['secretKey'],
            ],
        );
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function insertTransactionDetails(string $transactionDetails, string $transactionId): void
    {
        $this->connection->insert(
            'TransactionDetails',
            [
                'transactionId' => $transactionId,
                'details' => $transactionDetails,
            ],
        );
    }

    /**
     * @throws Exception
     */
    public function findTransaction(string $transactionId): ?array
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select(
                't.id',
                't.status',
                't.playerId',
                't.amount',
                'td.details AS paymentDetailsDecoded',
            )
            ->from('Transaction', 't')
            ->leftJoin('t', 'TransactionDetails', 'td', 'td.transactionId = t.id')
            ->where('t.id = :transactionId')
            ->setParameters(
                [
                    'transactionId' => $transactionId,
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchAssociative() ?: null;
    }

    /**
     * @throws Exception
     */
    public function getTransactionStatus(string $transactionId): ?string
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('t.status')
            ->from('Transaction', 't')
            ->where('t.id = :transactionId')
            ->setParameters(
                [
                    'transactionId' => $transactionId,
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchOne() ?: null;
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function changeTransactionStatus(string $transactionId, string $status): void
    {
        $this->connection->update('Transaction', ['status' => $status], ['id' => $transactionId]);
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function updateTransactionExternalId(string $transactionId, ?string $externalTransactionId): void
    {
        $this->connection->update(
            'Transaction',
            ['externalTransactionId' => $externalTransactionId],
            ['id' => $transactionId]
        );
    }

    /**
     * @throws Exception
     */
    public function findTransactionDetails(string $transactionId): ?string
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('td.details')
            ->from('TransactionDetails', 'td')
            ->where('td.transactionId = :transactionId')
            ->setParameters(
                [
                    'transactionId' => $transactionId,
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchOne() ?: null;
    }

    /**
     * @throws Exception
     */
    public function findTransactionDetailsKey(string $transactionId): ?string
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('tdk.decryptKey')
            ->from('TransactionDetailsKey', 'tdk')
            ->where('tdk.transactionId = :transactionId')
            ->setParameters(
                [
                    'transactionId' => $transactionId,
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchOne() ?: null;
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function createTransactionCascade(string $currentTransactionId, string $primalTransactionId): void
    {
        $this->connection->insert(
            'TransactionCascade',
            [
                'primalTransactionId' => $primalTransactionId,
                'currentTransactionId' => $currentTransactionId,
                'status' => CascadeTransactionStatusEnum::WAIT,
            ],
        );
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function cancelCascade(string $primalTransactionId): void
    {
        $this->connection->update(
            'TransactionCascade',
            [
                'status' => CascadeTransactionStatusEnum::CANCEL,
            ],
            [
                'primalTransactionId' => $primalTransactionId,
            ],
        );
    }

    /**
     * @throws Exception
     */
    public function findPrimalTransaction(string $transactionId): ?string
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('tc.primalTransactionId')
            ->from('TransactionCascade', 'tc')
            ->where('tc.currentTransactionId = :transactionId')
            ->setParameters(
                [
                    'transactionId' => $transactionId,
                ]
            )
        ;

        $stmt = $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters()
        );

        return $stmt->fetchOne() ?: null;
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function changeTransactionCascadeStatus(string $transactionId, string $status): void
    {
        $this->connection->update(
            'TransactionCascade',
            ['status' => $status],
            ['currentTransactionId' => $transactionId]
        );
    }

    /**
     * @throws Exception
     */
    public function deleteDecryptKey(string $transactionId): void
    {
        $this->connection->delete(
            'TransactionDetailsKey',
            ['transactionId' => $transactionId]
        );
    }
}
