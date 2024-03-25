<?php

declare(strict_types=1);

namespace App\ProjectCascade\GateWay\UseCase\CallbackHandler;

use App\ProjectCascade\Dto\PaymentDto;
use App\ProjectCascade\Enum\CascadeTransactionStatusEnum;
use App\ProjectCascade\Enum\TransactionStatusEnum;
use App\ProjectCascade\Exception\DBException;
use App\ProjectCascade\Exception\PaymentProviderNotFoundException;
use App\ProjectCascade\Exception\TransactionNotProcessableException;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerInterface;
use App\ProjectCascade\Service\BillingService;
use App\ProjectCascade\Service\IoCResolverService;
use Doctrine\DBAL\Exception as DbalException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Throwable;

class CallbackHandler implements GateWayHandlerInterface
{
    private BillingService $billingService;

    public function __construct()
    {
        /** @var BillingService $billingService */
        $billingService = IoCResolverService::getClass(BillingService::class);
        $this->billingService = $billingService;
    }

    public function authRequired(): bool
    {
        return false;
    }

    /**
     * @throws DbalException
     * @throws JsonException
     * @throws Throwable
     * @throws DBException
     * @throws PaymentProviderNotFoundException
     * @throws TransactionNotProcessableException
     */
    public function handle(Request $request, ?string $playerId = null): array
    {
        $jsonBody = $request->getBody()->getContents();
        $arrayBody = json_decode($jsonBody, true, 512, JSON_THROW_ON_ERROR);
        $transactionId = $arrayBody['transactionId'];

        switch ($arrayBody['status']) {
            case TransactionStatusEnum::CANCEL:
                $this->billingService->cancelTransaction($transactionId);

                $primalTransactionId = $this->billingService->findPrimalTransaction($transactionId);

                if ($primalTransactionId) {
                    $this->billingService->changeTransactionCascadeStatus($transactionId, CascadeTransactionStatusEnum::CANCEL);
                    $this->billingService->processTransaction(new PaymentDto(['providerName' => 'cascade']), $primalTransactionId);
                }

                break;
            case TransactionStatusEnum::DONE:
                $this->billingService->flushTransaction($transactionId);

                break;
            default:
                // no break
        }

        return [
            'action' => 'ok',
        ];
    }

    public static function getMethod(): string
    {
        return 'POST';
    }

    public static function getUri(): string
    {
        return 'callback';
    }
}
