<?php

namespace App\ProjectCascade\GateWay\UseCase\PaymentHandler;

use App\ProjectCascade\Dto\PaymentDto;
use App\ProjectCascade\Exception\PlayerNotFoundException;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerInterface;
use App\ProjectCascade\Service\BillingService;
use Doctrine\DBAL\Exception as DbalException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Throwable;

class GateWayPaymentHandler implements GateWayHandlerInterface
{
    private BillingService $billingService;

    public function __construct()
    {
        $this->billingService = new BillingService();
    }

    /**
     * @throws DbalException
     * @throws JsonException
     * @throws Throwable
     */
    public function handle(Request $request, ?string $playerId = null): array
    {
        if ($playerId === null) {
            throw new PlayerNotFoundException();
        }

        $jsonBody = $request->getBody()->getContents();
        $arrayBody = json_decode($jsonBody, true, 512, JSON_THROW_ON_ERROR);
        $dtoData = $arrayBody;
        $dtoData['playerId'] = $playerId;

        $paymentDto = new PaymentDto($dtoData);
        $transactionId = $this->billingService->prepareTransaction($paymentDto);
        $this->billingService->processTransaction($paymentDto, $transactionId);

        return [
            'action' => 'created',
            'source' => $transactionId,
        ];
    }

    public function authRequired(): bool
    {
        return true;
    }
}
