<?php

namespace App\DTOs;

class BudgetAllocationDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $amountType,
        public readonly float $amount,
        public readonly ?string $color,
        public readonly int $month,
        public readonly int $year,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            amountType: $data['amount_type'] ?? 'percentage',
            amount: $data['amount'],
            color: $data['color'] ?? null,
            month: $data['month'],
            year: $data['year'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'amount_type' => $this->amountType,
            'amount' => $this->amount,
            'color' => $this->color,
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}
