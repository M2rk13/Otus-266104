<?php

declare(strict_types=1);

namespace App\ProjectCascade\Dto;

class PaymentDto
{
    use DtoResolverTrait;

    private ?string $playerId;
    private string $providerName;
    private int $amount;
    private array $paymentDetails;

    public function getPlayerId(): ?string
    {
        return $this->playerId;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPaymentDetails(): array
    {
        return $this->paymentDetails;
    }
}
