<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::forUser($request->user()->id)
            ->orderBy('name')
            ->get();

        return Inertia::render('Wallets/Index', [
            'wallets' => $wallets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'balance' => ['required', 'numeric'],
            'is_active' => ['boolean'],
        ]);

        $request->user()->wallets()->create($validated);

        return redirect()->route('wallets.index')->with('success', 'Wallet created successfully.');
    }

    public function update(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'balance' => ['required', 'numeric'],
            'is_active' => ['boolean'],
        ]);

        $wallet->update($validated);

        return redirect()->route('wallets.index')->with('success', 'Wallet updated successfully.');
    }

    public function destroy(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== $request->user()->id) {
            abort(403);
        }

        $wallet->delete();

        return redirect()->route('wallets.index')->with('success', 'Wallet deleted successfully.');
    }
}
