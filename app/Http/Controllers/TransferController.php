<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $transfers = Transfer::forUser($userId)
            ->with(['fromWallet', 'toWallet'])
            ->orderByDesc('transfer_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        $wallets = Wallet::forUser($userId)->active()->orderBy('name')->get();

        return Inertia::render('Transfers/Index', [
            'transfers' => $transfers,
            'wallets' => $wallets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_wallet_id' => ['required', 'exists:wallets,id', 'different:to_wallet_id'],
            'to_wallet_id' => ['required', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transfer_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $fromWallet = Wallet::where('id', $validated['from_wallet_id'])->where('user_id', $request->user()->id)->firstOrFail();
        $toWallet = Wallet::where('id', $validated['to_wallet_id'])->where('user_id', $request->user()->id)->firstOrFail();

        DB::transaction(function () use ($request, $validated, $fromWallet, $toWallet) {
            $request->user()->transfers()->create($validated);
            
            $fromWallet->decrement('balance', $validated['amount']);
            $toWallet->increment('balance', $validated['amount']);
        });

        return redirect()->route('transfers.index')->with('success', 'Transfer created successfully.');
    }

    public function destroy(Request $request, Transfer $transfer)
    {
        if ($transfer->user_id !== $request->user()->id) {
            abort(403);
        }

        DB::transaction(function () use ($transfer) {
            $fromWallet = $transfer->fromWallet;
            $toWallet = $transfer->toWallet;

            // Reverse the transfer
            if ($fromWallet) $fromWallet->increment('balance', $transfer->amount);
            if ($toWallet) $toWallet->decrement('balance', $transfer->amount);

            $transfer->delete();
        });

        return redirect()->route('transfers.index')->with('success', 'Transfer deleted successfully.');
    }
}
