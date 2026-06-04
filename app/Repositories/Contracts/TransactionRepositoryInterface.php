<?php

namespace App\Repositories\Contracts;

use App\DTOs\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function getPaginatedForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getForUserMonth(int $userId, int $month, int $year): Collection;
    public function getForUserDateRange(int $userId, string $from, string $to): Collection;
    public function findById(int $id): ?Transaction;
    public function create(int $userId, TransactionDTO $dto): Transaction;
    public function update(Transaction $transaction, TransactionDTO $dto): Transaction;
    public function delete(Transaction $transaction): bool;
    public function getMonthlyTotals(int $userId, int $month, int $year): array;
    public function getMonthlyTotalsByCategory(int $userId, int $month, int $year): Collection;
    public function getTopSpendingCategories(int $userId, int $month, int $year, int $limit = 5): Collection;
    public function getRecentTransactions(int $userId, int $limit = 10): Collection;
    public function getMonthlySummary(int $userId, int $year): Collection;
}
