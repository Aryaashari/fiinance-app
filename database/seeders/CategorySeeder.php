<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $categories = [
            // Income categories
            ['name' => 'Salary', 'description' => 'Monthly salary income', 'color' => '#10b981', 'type' => 'income'],
            ['name' => 'Freelance', 'description' => 'Freelance and side project income', 'color' => '#34d399', 'type' => 'income'],
            ['name' => 'Bonus', 'description' => 'Performance bonuses and rewards', 'color' => '#6ee7b7', 'type' => 'income'],
            ['name' => 'Other Income', 'description' => 'Other sources of income', 'color' => '#059669', 'type' => 'income'],

            // Expense categories
            ['name' => 'Food & Drink', 'description' => 'Groceries, dining, and food delivery', 'color' => '#f59e0b', 'type' => 'expense'],
            ['name' => 'Transportation', 'description' => 'Fuel, public transport, ride-hailing', 'color' => '#3b82f6', 'type' => 'expense'],
            ['name' => 'Housing', 'description' => 'Rent, mortgage, maintenance', 'color' => '#8b5cf6', 'type' => 'expense'],
            ['name' => 'Bills & Utilities', 'description' => 'Electricity, water, internet, phone', 'color' => '#06b6d4', 'type' => 'expense'],
            ['name' => 'Shopping', 'description' => 'Clothing, electronics, personal items', 'color' => '#ec4899', 'type' => 'expense'],
            ['name' => 'Entertainment', 'description' => 'Movies, games, hobbies', 'color' => '#f43f5e', 'type' => 'expense'],
            ['name' => 'Health', 'description' => 'Medical, pharmacy, fitness', 'color' => '#ef4444', 'type' => 'expense'],
            ['name' => 'Education', 'description' => 'Tuition, courses, books', 'color' => '#a855f7', 'type' => 'expense'],
            ['name' => 'Other Expense', 'description' => 'Other miscellaneous expenses', 'color' => '#64748b', 'type' => 'expense'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'user_id' => $user->id,
                ...$category,
            ]);
        }
    }
}
