<?php

namespace App\DTOs;

class TransactionDTO
{
    public function __construct(
        public readonly ?int $walletId,
        public readonly int $categoryId,
        public readonly float $amount,
        public readonly string $transactionDate,
        public readonly ?string $notes = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            walletId: $data['wallet_id'] ?? null,
            categoryId: $data['category_id'],
            amount: $data['amount'],
            transactionDate: $data['transaction_date'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'wallet_id' => $this->walletId,
            'category_id' => $this->categoryId,
            'amount' => $this->amount,
            'transaction_date' => $this->transactionDate,
            'notes' => $this->notes,
        ];
    }
}
