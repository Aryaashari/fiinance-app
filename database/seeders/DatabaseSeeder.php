<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@finance.app',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            CategorySeeder::class,
            TransactionSeeder::class, // Call this before budget allocation if budget allocation depends on transactions (though it doesn't here)
            BudgetAllocationSeeder::class,
            SavingsGoalSeeder::class,
            InvestmentSeeder::class,
        ]);
    }
}
