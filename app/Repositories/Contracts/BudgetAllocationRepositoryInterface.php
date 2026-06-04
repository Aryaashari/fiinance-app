<?php

namespace App\Repositories\Contracts;

use App\DTOs\BudgetAllocationDTO;
use App\Models\BudgetAllocation;
use Illuminate\Database\Eloquent\Collection;

interface BudgetAllocationRepositoryInterface
{
    public function getForUserMonth(int $userId, int $month, int $year): Collection;
    public function findById(int $id): ?BudgetAllocation;
    public function create(int $userId, BudgetAllocationDTO $dto): BudgetAllocation;
    public function update(BudgetAllocation $allocation, BudgetAllocationDTO $dto): BudgetAllocation;
    public function delete(BudgetAllocation $allocation): bool;
    public function bulkUpsert(int $userId, int $month, int $year, array $allocations): Collection;
    public function getTotalPercentage(int $userId, int $month, int $year, ?int $excludeId = null): float;
}
