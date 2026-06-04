<?php

namespace App\Services;

use App\DTOs\TransactionDTO;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;

class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function getPaginated(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->transactionRepository->getPaginatedForUser($userId, $filters, $perPage);
    }

    public function create(int $userId, TransactionDTO $dto): Transaction
    {
        return DB::transaction(function () use ($userId, $dto) {
            $transaction = $this->transactionRepository->create($userId, $dto);
            $this->updateWalletBalance($transaction, true);
            return $transaction;
        });
    }

    public function update(Transaction $transaction, TransactionDTO $dto): Transaction
    {
        return DB::transaction(function () use ($transaction, $dto) {
            // Reverse old transaction effect
            $this->updateWalletBalance($transaction, false);
            
            // Update transaction
            $updatedTransaction = $this->transactionRepository->update($transaction, $dto);
            
            // Apply new transaction effect
            $this->updateWalletBalance($updatedTransaction, true);
            
            return $updatedTransaction;
        });
    }

    public function delete(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $this->updateWalletBalance($transaction, false);
            return $this->transactionRepository->delete($transaction);
        });
    }

    private function updateWalletBalance(Transaction $transaction, bool $isAdding): void
    {
        if (!$transaction->wallet_id) {
            return;
        }

        $wallet = Wallet::find($transaction->wallet_id);
        if (!$wallet) {
            return;
        }

        $amount = $transaction->amount;
        if (!$isAdding) {
            $amount = -$amount;
        }

        if ($transaction->isExpense()) {
            $wallet->decrement('balance', $amount);
        } elseif ($transaction->isIncome()) {
            $wallet->increment('balance', $amount);
        }
    }

    public function getMonthlyTotals(int $userId, int $month, int $year): array
    {
        return $this->transactionRepository->getMonthlyTotals($userId, $month, $year);
    }

    public function getRecentTransactions(int $userId, int $limit = 10): Collection
    {
        return $this->transactionRepository->getRecentTransactions($userId, $limit);
    }

    public function getForUserMonth(int $userId, int $month, int $year): Collection
    {
        return $this->transactionRepository->getForUserMonth($userId, $month, $year);
    }

    public function getForUserDateRange(int $userId, string $from, string $to): Collection
    {
        return $this->transactionRepository->getForUserDateRange($userId, $from, $to);
    }

    public function getMonthlySummary(int $userId, int $year): Collection
    {
        return $this->transactionRepository->getMonthlySummary($userId, $year);
    }
}
