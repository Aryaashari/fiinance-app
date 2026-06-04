<?php

namespace App\Repositories\Eloquent;

use App\DTOs\TransactionDTO;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getPaginatedForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::forUser($userId)
            ->with(['category', 'wallet'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'income') {
                $query->income();
            } elseif ($filters['type'] === 'expense') {
                $query->expense();
            }
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->forDateRange($filters['date_from'], $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $query->where('notes', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getForUserMonth(int $userId, int $month, int $year): Collection
    {
        return Transaction::forUser($userId)
            ->forMonth($month, $year)
            ->with(['category', 'wallet'])
            ->orderByDesc('transaction_date')
            ->get();
    }

    public function getForUserDateRange(int $userId, string $from, string $to): Collection
    {
        return Transaction::forUser($userId)
            ->forDateRange($from, $to)
            ->with(['category', 'wallet'])
            ->orderByDesc('transaction_date')
            ->get();
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::with(['category', 'wallet'])->find($id);
    }

    public function create(int $userId, TransactionDTO $dto): Transaction
    {
        return Transaction::create([
            'user_id' => $userId,
            ...$dto->toArray(),
        ])->load(['category', 'wallet']);
    }

    public function update(Transaction $transaction, TransactionDTO $dto): Transaction
    {
        $transaction->update($dto->toArray());
        return $transaction->fresh()->load(['category', 'wallet']);
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    public function getMonthlyTotals(int $userId, int $month, int $year): array
    {
        $totals = Transaction::forUser($userId)
            ->forMonth($month, $year)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw("
                SUM(CASE WHEN categories.type = 'income' THEN transactions.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
            ")
            ->first();

        return [
            'total_income' => (float) ($totals->total_income ?? 0),
            'total_expense' => (float) ($totals->total_expense ?? 0),
            'net_cash_flow' => (float) (($totals->total_income ?? 0) - ($totals->total_expense ?? 0)),
        ];
    }

    public function getMonthlyTotalsByCategory(int $userId, int $month, int $year): Collection
    {
        return Transaction::forUser($userId)
            ->forMonth($month, $year)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category_id')
            ->get();
    }

    public function getTopSpendingCategories(int $userId, int $month, int $year, int $limit = 5): Collection
    {
        return Transaction::forUser($userId)
            ->forMonth($month, $year)
            ->expense()
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get();
    }

    public function getRecentTransactions(int $userId, int $limit = 10): Collection
    {
        return Transaction::forUser($userId)
            ->with(['category', 'wallet'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getMonthlySummary(int $userId, int $year): Collection
    {
        return Transaction::forUser($userId)
            ->whereYear('transaction_date', $year)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                DB::raw('MONTH(transactions.transaction_date) as month'),
                DB::raw("SUM(CASE WHEN categories.type = 'income' THEN transactions.amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
