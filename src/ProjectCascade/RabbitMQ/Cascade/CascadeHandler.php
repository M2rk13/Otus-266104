<?php

namespace App\ProjectCascade\RabbitMQ\Cascade;

use App\ProjectCascade\Billing\PaymentSystem\BillingBundle\PaymentSystemProviderInterface;
use App\ProjectCascade\Billing\Registry\ProviderRegistry;
use App\ProjectCascade\Dto\PaymentDto;
use App\ProjectCascade\Enum\CascadeTransactionStatusEnum;
use App\ProjectCascade\Exception\PaymentProviderNotFoundException;
use App\ProjectCascade\Exception\TransactionNotProcessableException;
use App\ProjectCascade\RabbitMQ\RabbitDtoInterface;
use App\ProjectCascade\RabbitMQ\RabbitHandlerInterface;
use App\ProjectCascade\Service\BillingService;
use Doctrine\DBAL\Exception;
use Throwable;

class CascadeHandler implements RabbitHandlerInterface
{
    private CascadeManager $manager;
    private ProviderRegistry $registry;
    private BillingService $billingService;

    public function __construct()
    {
        $this->manager = new CascadeManager();
        $this->registry = new ProviderRegistry();
        $this->billingService = new BillingService();
    }

    /**
     * @param CascadeMessageDto $dto
     *
     * @throws PaymentProviderNotFoundException
     * @throws Throwable
     * @throws TransactionNotProcessableException
     * @throws Exception
     */
    public function handle(RabbitDtoInterface $dto): void
    {
        $primalTransactionId = $dto->getTransactionId();

        $processedProviderList = $this->manager->getProviderProcessedListId($primalTransactionId);
        $cascadeProviderList = $this->manager->getProviderListToProcess($processedProviderList);
        $resultProviderList = array_diff($cascadeProviderList, $processedProviderList);

        $provider = $this->getProvider($resultProviderList);

        if ($provider === null) {
            $this->billingService->cancelTransaction($primalTransactionId);
            $this->billingService->cancelCascade($primalTransactionId);

            return;
        }

        $transactionDto = $this->billingService->getTransactionDto($primalTransactionId);

        $paymentDto = new PaymentDto([
            'playerId' => $transactionDto->getPlayerId(),
            'providerName' => $provider->getName(),
            'amount' => $transactionDto->getAmount(),
            'paymentDetails' => $transactionDto->getPaymentDetails(),
        ]);

        $transactionId = $this->billingService->prepareTransaction($paymentDto);
        $this->billingService->registerTransactionCascade($transactionId, $primalTransactionId);

        try {
            $this->billingService->processTransaction($paymentDto, $transactionId);
        } catch (Throwable $e) {
            // log exception

            $this->billingService->changeTransactionCascadeStatus($transactionId, CascadeTransactionStatusEnum::CANCEL);
            $this->handle($dto);
        }
    }

    /**
     * @throws PaymentProviderNotFoundException
     */
    private function getProvider(array $cascadeProviderList): ?PaymentSystemProviderInterface
    {
        if (empty($cascadeProviderList)) {
            return null;
        }

        $key = array_rand($cascadeProviderList);
        $providerName = $cascadeProviderList[$key];

        return $this->registry->getProvider($providerName);
    }
}
