<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $categories = Category::where('user_id', $user->id)->get();

        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        // Generate 3 months of transactions
        for ($monthOffset = 2; $monthOffset >= 0; $monthOffset--) {
            $date = Carbon::now()->subMonths($monthOffset);
            $month = $date->month;
            $year = $date->year;

            // Income transactions
            $salary = $incomeCategories->firstWhere('name', 'Salary');
            if ($salary) {
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $salary->id,
                    'amount' => 15000000,
                    'transaction_date' => Carbon::create($year, $month, 1),
                    'notes' => 'Monthly salary',
                ]);
            }

            $freelance = $incomeCategories->firstWhere('name', 'Freelance');
            if ($freelance) {
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $freelance->id,
                    'amount' => rand(2000000, 5000000),
                    'transaction_date' => Carbon::create($year, $month, rand(5, 25)),
                    'notes' => 'Freelance project payment',
                ]);
            }

            // Expense transactions
            $this->createExpenseTransactions($user, $expenseCategories, $month, $year);
        }
    }

    private function createExpenseTransactions($user, $expenseCategories, int $month, int $year): void
    {
        $expenseData = [
            'Food & Drink' => [
                ['amount' => rand(800000, 1500000), 'day' => rand(1, 7), 'notes' => 'Weekly groceries'],
                ['amount' => rand(500000, 1000000), 'day' => rand(8, 14), 'notes' => 'Weekly groceries'],
                ['amount' => rand(200000, 500000), 'day' => rand(10, 20), 'notes' => 'Dining out'],
                ['amount' => rand(300000, 800000), 'day' => rand(15, 21), 'notes' => 'Weekly groceries'],
                ['amount' => rand(400000, 900000), 'day' => rand(22, 28), 'notes' => 'Weekly groceries'],
            ],
            'Transportation' => [
                ['amount' => rand(200000, 500000), 'day' => rand(1, 10), 'notes' => 'Fuel'],
                ['amount' => rand(100000, 300000), 'day' => rand(11, 20), 'notes' => 'Ride-hailing'],
                ['amount' => rand(200000, 400000), 'day' => rand(21, 28), 'notes' => 'Fuel'],
            ],
            'Bills & Utilities' => [
                ['amount' => 350000, 'day' => 10, 'notes' => 'Internet subscription'],
                ['amount' => 150000, 'day' => 10, 'notes' => 'Phone plan'],
                ['amount' => 450000, 'day' => 15, 'notes' => 'Electricity bill'],
            ],
            'Housing' => [
                ['amount' => 1500000, 'day' => 1, 'notes' => 'Monthly rent'],
            ],
            'Other Expense' => [
                ['amount' => rand(100000, 250000), 'day' => rand(5, 15), 'notes' => 'Laundry service'],
                ['amount' => rand(100000, 200000), 'day' => rand(16, 25), 'notes' => 'Laundry service'],
            ],
            'Entertainment' => [
                ['amount' => rand(100000, 300000), 'day' => rand(10, 20), 'notes' => 'Movies and streaming'],
                ['amount' => rand(50000, 200000), 'day' => rand(15, 25), 'notes' => 'Gaming subscription'],
            ],
        ];

        foreach ($expenseData as $categoryName => $transactions) {
            $category = $expenseCategories->firstWhere('name', $categoryName);
            if (!$category) continue;

            foreach ($transactions as $txn) {
                $day = min($txn['day'], Carbon::create($year, $month)->daysInMonth);
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'amount' => $txn['amount'],
                    'transaction_date' => Carbon::create($year, $month, $day),
                    'notes' => $txn['notes'],
                ]);
            }
        }
    }
}
