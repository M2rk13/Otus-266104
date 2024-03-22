<?php

namespace App\ProjectCascade\Dto;

class TransactionDto
{
    use DtoResolverTrait;

    private string $id;
    private int $amount;
    private ?string $playerId;
    private string $status;
    private ?array $paymentDetails;

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPlayerId(): ?string
    {
        return $this->playerId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPaymentDetails(): ?array
    {
        return $this->paymentDetails;
    }
}
