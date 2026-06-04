<?php

namespace Database\Seeders;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvestmentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $investments = [
            [
                'name' => 'BBCA Stock',
                'type' => 'Stocks',
                'allocated_amount' => 20000000,
                'current_value' => 22500000,
                'color' => '#3b82f6',
                'notes' => 'Long term hold',
            ],
            [
                'name' => 'S&P 500 Index Fund',
                'type' => 'Mutual Funds',
                'allocated_amount' => 15000000,
                'current_value' => 16200000,
                'color' => '#10b981',
                'notes' => 'Diversification',
            ],
            [
                'name' => 'Physical Gold',
                'type' => 'Gold',
                'allocated_amount' => 5000000,
                'current_value' => 5400000,
                'color' => '#f59e0b',
                'notes' => 'Safe haven',
            ],
            [
                'name' => 'Bitcoin',
                'type' => 'Crypto',
                'allocated_amount' => 3000000,
                'current_value' => 4500000,
                'color' => '#8b5cf6',
                'notes' => 'High risk',
            ],
        ];

        foreach ($investments as $investmentData) {
            Investment::create(array_merge(['user_id' => $user->id], $investmentData));
        }
    }
}
