<?php

declare(strict_types=1);

namespace App\ProjectCascade\Service;

use App\ProjectCascade\Billing\Registry\ProviderRegistry;
use App\ProjectCascade\DBMaintence\DBManager;
use App\ProjectCascade\DBMaintence\DBTransactionServiceInterface;
use App\ProjectCascade\Dto\PaymentDto;
use App\ProjectCascade\Dto\ProtectedDto;
use App\ProjectCascade\Dto\TransactionDto;
use App\ProjectCascade\Enum\CascadeTransactionStatusEnum;
use App\ProjectCascade\Enum\TransactionStatusEnum;
use App\ProjectCascade\Exception\DBException;
use App\ProjectCascade\Exception\PaymentProviderNotFoundException;
use App\ProjectCascade\Exception\TransactionNotFoundException;
use App\ProjectCascade\Exception\TransactionNotProcessableException;
use Doctrine\DBAL\Exception;
use JsonException;
use Throwable;

class BillingService
{
    /** @var BillingServiceManager  */
    private DBManager $manager;
    private ProviderRegistry $providerRegistry;
    private DBTransactionServiceInterface $transactionService;

    public function __construct()
    {
        /** @var ProviderRegistry $providerRegistry */
        $providerRegistry = IoCResolverService::getClass(ProviderRegistry::class);
        $this->providerRegistry = $providerRegistry;
        $this->transactionService = IoCResolverService::getTransactionService();
        $this->manager = IoCResolverService::getManager(self::class);
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws JsonException
     */
    public function prepareTransaction(PaymentDto $paymentDto): string
    {
        $providerId = $this->manager->findProviderId($paymentDto->getProviderName());

        $secretKey = IdGenerator::generateRandomString();
        $transactionDetails = json_encode($paymentDto->getPaymentDetails(), JSON_THROW_ON_ERROR);
        $transactionDetails = openssl_encrypt($transactionDetails, 'aes-256-cbc', $secretKey);
        $protectedKey = new ProtectedDto(['data' => ['secretKey' => $secretKey]]);

        $this->transactionService->beginTransaction();

        try {
            $transactionId = $this->manager->createTransaction($paymentDto, $providerId);

            $this->manager->insertTransactionDetailsKey($protectedKey, $transactionId);
            $this->manager->insertTransactionDetails($transactionDetails, $transactionId);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }

        return $transactionId;
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws TransactionNotProcessableException
     * @throws PaymentProviderNotFoundException
     */
    public function processTransaction(PaymentDto $paymentDto, string $transactionId): void
    {
        $provider = $this->providerRegistry->getProvider($paymentDto->getProviderName());

        try {
            $externalTransactionId = $provider->requestPayment($transactionId);
        } catch (Throwable $e) {
            $this->cancelTransaction($transactionId);

            throw $e;
        }

        $this->setTransactionToWait($transactionId, $externalTransactionId);
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws TransactionNotProcessableException
     */
    public function cancelTransaction(string $transactionId): void
    {
        $this->checkTransactionNextStatus($transactionId, TransactionStatusEnum::CANCEL);

        $this->transactionService->beginTransaction();

        try {
            $this->manager->changeTransactionStatus($transactionId, TransactionStatusEnum::CANCEL);
            $this->manager->deleteDecryptKey($transactionId);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     * @throws TransactionNotProcessableException
     */
    private function setTransactionToWait(string $transactionId, ?string $externalTransactionId): void
    {
        $this->transactionService->beginTransaction();

        try {
            $this->manager->changeTransactionStatus($transactionId, TransactionStatusEnum::WAIT);
            $this->manager->updateTransactionExternalId($transactionId, $externalTransactionId);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }
    }

    /**
     * @throws Exception
     * @throws TransactionNotProcessableException
     */
    private function checkTransactionNextStatus(string $transactionId, string $nextStatus): void
    {
        $currentStatus = $this->manager->getTransactionStatus($transactionId);

        $possibleStatusList = $this->getNextTransactionStatusMap($currentStatus);

        if (!in_array($nextStatus, $possibleStatusList, true)) {
            throw new TransactionNotProcessableException(sprintf(
                'transaction [%s], currentStatus [%s], nextStatus [%s]',
                $transactionId,
                $currentStatus,
                $nextStatus
            ));
        }
    }

    private function getNextTransactionStatusMap(string $status): array
    {
        return match ($status) {
            TransactionStatusEnum::NEW => [
                TransactionStatusEnum::WAIT,
                TransactionStatusEnum::CANCEL,
            ],
            TransactionStatusEnum::WAIT => [
                TransactionStatusEnum::DONE,
                TransactionStatusEnum::CANCEL,
            ],
            default => [],
        };
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function getTransactionDetails(string $transactionId): ProtectedDto
    {
        $transactionDetails = $this->manager->findTransactionDetails($transactionId);
        $transactionDetailsKey = $this->manager->findTransactionDetailsKey($transactionId);
        $transactionDetailsResult = null;

        if (!empty($transactionDetails) && !empty($transactionDetailsKey)) {
            $transactionDetailsResult = openssl_decrypt($transactionDetails, 'aes-256-cbc', $transactionDetailsKey);
        }

        return new ProtectedDto([
            'data' => [
                'transactionDetails' => json_decode($transactionDetailsResult, true, 512, JSON_THROW_ON_ERROR),
            ],
        ]);
    }

    /**
     * @throws Exception
     * @throws TransactionNotFoundException
     */
    public function getTransactionDto(string $transactionId): TransactionDto
    {
        $transactionData = $this->manager->findTransaction($transactionId);

        if ($transactionData === null) {
            throw new TransactionNotFoundException();
        }

        $transactionDetailsProtectedDto = $this->getTransactionDetails($transactionId);

        return new TransactionDto([
            'id' => (string) $transactionData['id'],
            'amount' => $transactionData['amount'],
            'status' => $transactionData['status'],
            'playerId' => (string) $transactionData['playerId'],
            'paymentDetails' => $transactionDetailsProtectedDto->getData()['transactionDetails'],
        ]);
    }

    /**
     * @throws Exception
     * @throws DBException
     */
    public function registerTransactionCascade(string $currentTransactionId, string $primalTransactionId): void
    {
        $this->transactionService->beginTransaction();

        try {
            $this->manager->createTransactionCascade($currentTransactionId, $primalTransactionId);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }
    }

    /**
     * @throws DBException
     * @throws Exception
     * @throws Throwable
     */
    public function cancelCascade(string $primalTransactionId): void
    {
        $this->transactionService->beginTransaction();

        try {
            $this->manager->cancelCascade($primalTransactionId);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function findPrimalTransaction(string $transactionId): ?string
    {
        return $this->manager->findPrimalTransaction($transactionId);
    }

    /**
     * @throws DBException
     * @throws Exception
     * @throws Throwable
     */
    public function flushTransaction(string $transactionId): void
    {
        $this->checkTransactionNextStatus($transactionId, TransactionStatusEnum::CANCEL);

        $this->transactionService->beginTransaction();

        try {
            $this->manager->changeTransactionStatus($transactionId, TransactionStatusEnum::DONE);
            $this->manager->deleteDecryptKey($transactionId);

            $primalTransactionId = $this->findPrimalTransaction($transactionId);

            if ($primalTransactionId) {
                $this->changeTransactionCascadeStatus($transactionId, CascadeTransactionStatusEnum::DONE);
                $this->manager->changeTransactionStatus($primalTransactionId, TransactionStatusEnum::DONE);
                $this->manager->deleteDecryptKey($primalTransactionId);
            }

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollback();

            throw $e;
        }
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function changeTransactionCascadeStatus(string $transactionId, string $status): void
    {
        $this->manager->changeTransactionCascadeStatus($transactionId, $status);
    }
}
