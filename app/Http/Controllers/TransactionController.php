<?php

namespace App\Http\Controllers;

use App\DTOs\TransactionDTO;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\CategoryService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private CategoryService $categoryService,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $filters = $request->only(['category_id', 'type', 'date_from', 'date_to', 'search']);
        $transactions = $this->transactionService->getPaginated($userId, $filters);
        $categories = $this->categoryService->getActiveForUser($userId);
        $wallets = \App\Models\Wallet::forUser($userId)->active()->orderBy('name')->get();

        $month = now()->month;
        $year = now()->year;
        $monthlyTotals = $this->transactionService->getMonthlyTotals($userId, $month, $year);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'wallets' => $wallets,
            'monthlyTotals' => $monthlyTotals,
            'filters' => $filters,
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $dto = TransactionDTO::fromRequest($request->validated());
        $this->transactionService->create($request->user()->id, $dto);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $dto = TransactionDTO::fromRequest($request->validated());
        $this->transactionService->update($transaction, $dto);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->delete($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}
