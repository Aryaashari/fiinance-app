<?php

namespace Database\Seeders;

use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SavingsGoalSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $goals = [
            [
                'name' => 'Emergency Fund',
                'target_amount' => 50000000,
                'current_amount' => 12000000,
                'target_date' => Carbon::now()->addMonths(12),
                'color' => '#ef4444',
                'notes' => '6 months of living expenses',
            ],
            [
                'name' => 'New Laptop',
                'target_amount' => 15000000,
                'current_amount' => 5000000,
                'target_date' => Carbon::now()->addMonths(6),
                'color' => '#3b82f6',
                'notes' => 'For work and personal projects',
            ],
            [
                'name' => 'Vacation Fund',
                'target_amount' => 10000000,
                'current_amount' => 2500000,
                'target_date' => Carbon::now()->addMonths(8),
                'color' => '#10b981',
                'notes' => 'Trip to Bali',
            ],
        ];

        foreach ($goals as $goalData) {
            $goal = SavingsGoal::create(array_merge(['user_id' => $user->id], $goalData));
            
            // Add some contribution history
            $goal->contributions()->create([
                'amount' => $goalData['current_amount'] / 2,
                'date' => Carbon::now()->subMonths(2),
                'notes' => 'Initial saving',
            ]);
            
            $goal->contributions()->create([
                'amount' => $goalData['current_amount'] / 2,
                'date' => Carbon::now()->subMonths(1),
                'notes' => 'Monthly contribution',
            ]);
        }
    }
}
