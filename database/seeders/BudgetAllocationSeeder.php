<?php

namespace Database\Seeders;

use App\Models\BudgetAllocation;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetAllocationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $month = now()->month;
        $year = now()->year;

        $allocations = [
            ['name' => 'Food Budget', 'amount_type' => 'percentage', 'amount' => 25, 'color' => '#f59e0b'],
            ['name' => 'Savings', 'amount_type' => 'percentage', 'amount' => 20, 'color' => '#3b82f6'],
            ['name' => 'Investment', 'amount_type' => 'percentage', 'amount' => 15, 'color' => '#10b981'],
            ['name' => 'Transportation', 'amount_type' => 'percentage', 'amount' => 10, 'color' => '#8b5cf6'],
            ['name' => 'Entertainment', 'amount_type' => 'percentage', 'amount' => 5, 'color' => '#ec4899'],
            ['name' => 'Bills & Utilities', 'amount_type' => 'percentage', 'amount' => 15, 'color' => '#06b6d4'],
            ['name' => 'Housing', 'amount_type' => 'percentage', 'amount' => 10, 'color' => '#a855f7'],
        ];

        foreach ($allocations as $allocation) {
            BudgetAllocation::create([
                'user_id' => $user->id,
                'name' => $allocation['name'],
                'amount_type' => $allocation['amount_type'],
                'amount' => $allocation['amount'],
                'color' => $allocation['color'],
                'month' => $month,
                'year' => $year,
            ]);
        }
    }
}
