<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index(Request $request)
    {
        $savingsGoals = SavingsGoal::where('user_id', $request->user()->id)->get();
        return response()->json($savingsGoals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date',
            'color' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $savingsGoal = $request->user()->savingsGoals()->create($validated);

        return response()->json($savingsGoal, 201);
    }

    public function destroy(Request $request, SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== $request->user()->id) {
            abort(403);
        }
        $savingsGoal->delete();
        return response()->json(null, 204);
    }
}
