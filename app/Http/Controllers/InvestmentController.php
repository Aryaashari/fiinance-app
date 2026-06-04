<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $investments = Investment::where('user_id', $request->user()->id)->get();
        return response()->json($investments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'allocated_amount' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'color' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $investment = $request->user()->investments()->create($validated);

        return response()->json($investment, 201);
    }

    public function destroy(Request $request, Investment $investment)
    {
        if ($investment->user_id !== $request->user()->id) {
            abort(403);
        }
        $investment->delete();
        return response()->json(null, 204);
    }
}
