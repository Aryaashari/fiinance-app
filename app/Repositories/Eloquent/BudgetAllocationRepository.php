<?php

namespace App\Repositories\Eloquent;

use App\DTOs\BudgetAllocationDTO;
use App\Models\BudgetAllocation;
use App\Repositories\Contracts\BudgetAllocationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BudgetAllocationRepository implements BudgetAllocationRepositoryInterface
{
    public function getForUserMonth(int $userId, int $month, int $year): Collection
    {
        return BudgetAllocation::forUser($userId)
            ->forMonth($month, $year)
            ->get();
    }

    public function findById(int $id): ?BudgetAllocation
    {
        return BudgetAllocation::find($id);
    }

    public function create(int $userId, BudgetAllocationDTO $dto): BudgetAllocation
    {
        return BudgetAllocation::create([
            'user_id' => $userId,
            ...$dto->toArray(),
        ]);
    }

    public function update(BudgetAllocation $allocation, BudgetAllocationDTO $dto): BudgetAllocation
    {
        $allocation->update($dto->toArray());
        return $allocation->fresh();
    }

    public function delete(BudgetAllocation $allocation): bool
    {
        return $allocation->delete();
    }

    public function bulkUpsert(int $userId, int $month, int $year, array $allocations): Collection
    {
        // This is simplified since category_id is removed
        foreach ($allocations as $allocationData) {
            BudgetAllocation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'name' => $allocationData['name'],
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'amount_type' => $allocationData['amount_type'] ?? 'percentage',
                    'amount' => $allocationData['amount'],
                    'color' => $allocationData['color'] ?? null,
                ]
            );
        }

        return $this->getForUserMonth($userId, $month, $year);
    }

    public function getTotalPercentage(int $userId, int $month, int $year, ?int $excludeId = null): float
    {
        $query = BudgetAllocation::forUser($userId)
            ->forMonth($month, $year)
            ->where('amount_type', 'percentage');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return (float) $query->sum('amount');
    }
}
