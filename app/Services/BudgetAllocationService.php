<?php

namespace App\Services;

use App\DTOs\BudgetAllocationDTO;
use App\Models\BudgetAllocation;
use App\Repositories\Contracts\BudgetAllocationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BudgetAllocationService
{
    public function __construct(
        private BudgetAllocationRepositoryInterface $budgetRepository
    ) {}

    public function getForUserMonth(int $userId, int $month, int $year): Collection
    {
        return $this->budgetRepository->getForUserMonth($userId, $month, $year);
    }

    public function create(int $userId, BudgetAllocationDTO $dto): BudgetAllocation
    {
        $this->validateTotalPercentage($userId, $dto->month, $dto->year, $dto->percentage);

        return $this->budgetRepository->create($userId, $dto);
    }

    public function update(BudgetAllocation $allocation, BudgetAllocationDTO $dto): BudgetAllocation
    {
        $this->validateTotalPercentage(
            $allocation->user_id,
            $dto->month,
            $dto->year,
            $dto->percentage,
            $allocation->id
        );

        return $this->budgetRepository->update($allocation, $dto);
    }

    public function delete(BudgetAllocation $allocation): bool
    {
        return $this->budgetRepository->delete($allocation);
    }

    public function bulkUpdate(int $userId, int $month, int $year, array $allocations): Collection
    {
        $total = collect($allocations)->sum('percentage');

        if ($total > 100) {
            throw ValidationException::withMessages([
                'percentage' => "Total allocation percentage cannot exceed 100%. Current total: {$total}%.",
            ]);
        }

        return $this->budgetRepository->bulkUpsert($userId, $month, $year, $allocations);
    }

    public function getTotalPercentage(int $userId, int $month, int $year): float
    {
        return $this->budgetRepository->getTotalPercentage($userId, $month, $year);
    }

    private function validateTotalPercentage(int $userId, int $month, int $year, float $newPercentage, ?int $excludeId = null): void
    {
        $currentTotal = $this->budgetRepository->getTotalPercentage($userId, $month, $year, $excludeId);
        $newTotal = $currentTotal + $newPercentage;

        if ($newTotal > 100) {
            $remaining = 100 - $currentTotal;
            throw ValidationException::withMessages([
                'percentage' => "Total allocation cannot exceed 100%. Remaining available: {$remaining}%.",
            ]);
        }
    }
}
