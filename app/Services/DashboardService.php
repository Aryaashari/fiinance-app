<?php

namespace App\Services;

use App\Models\BudgetAllocation;
use App\Models\Investment;
use App\Models\SavingsGoal;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function getDashboardData(int $userId): array
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $monthlyTotals = $this->transactionRepository->getMonthlyTotals($userId, $month, $year);
        $categoryTotals = $this->transactionRepository->getMonthlyTotalsByCategory($userId, $month, $year);
        $topSpending = $this->transactionRepository->getTopSpendingCategories($userId, $month, $year);
        $recentTransactions = $this->transactionRepository->getRecentTransactions($userId, 10);
        $monthlySummary = $this->transactionRepository->getMonthlySummary($userId, $year);
        $categories = $this->categoryRepository->getAllForUser($userId);

        // Fetch new modules
        $budgetAllocations = BudgetAllocation::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->get();
            
        $savingsGoals = SavingsGoal::where('user_id', $userId)->get();
        $investments = Investment::where('user_id', $userId)->get();

        // Format Budget Allocations
        $formattedBudgets = [];
        $totalIncome = (float) $monthlyTotals['total_income'];
        
        foreach ($budgetAllocations as $allocation) {
            $budgetAmount = $allocation->amount_type === 'percentage' 
                ? ($allocation->amount / 100) * $totalIncome
                : $allocation->amount;

            $formattedBudgets[] = [
                'id' => $allocation->id,
                'name' => $allocation->name,
                'color' => $allocation->color,
                'amount_type' => $allocation->amount_type,
                'amount' => (float) $allocation->amount,
                'calculated_budget' => $budgetAmount,
            ];
        }

        // Calculate financial insights
        $insights = $this->calculateInsights($monthlyTotals, $categories);

        // Build chart data
        $charts = $this->buildChartData($monthlySummary, $categoryTotals, $categories);

        return [
            'stats' => [
                'total_income' => $monthlyTotals['total_income'],
                'total_expense' => $monthlyTotals['total_expense'],
                'net_cash_flow' => $monthlyTotals['net_cash_flow'],
                'remaining_budget' => $monthlyTotals['total_income'] - $monthlyTotals['total_expense'],
                'total_savings' => $savingsGoals->sum('current_amount'),
                'total_investments' => $investments->sum('current_value'),
            ],
            'charts' => $charts,
            'insights' => $insights,
            'recent_transactions' => $recentTransactions,
            'budget_allocations' => $formattedBudgets,
            'savings_goals' => $savingsGoals,
            'investments' => $investments,
            'top_spending' => $topSpending,
            'current_month' => $month,
            'current_year' => $year,
        ];
    }

    private function calculateInsights(array $monthlyTotals, $categories): array
    {
        $insights = [];

        $savingsRate = $monthlyTotals['total_income'] > 0
            ? round((($monthlyTotals['total_income'] - $monthlyTotals['total_expense']) / $monthlyTotals['total_income']) * 100, 1)
            : 0;

        $insights[] = [
            'type' => $savingsRate >= 20 ? 'success' : ($savingsRate >= 10 ? 'info' : 'warning'),
            'title' => 'Savings Rate',
            'message' => "Your savings rate this month is {$savingsRate}%.",
            'value' => $savingsRate,
        ];

        return $insights;
    }

    private function buildChartData($monthlySummary, $categoryTotals, $categories): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $incomeData = array_fill(0, 12, 0);
        $expenseData = array_fill(0, 12, 0);

        foreach ($monthlySummary as $summary) {
            $idx = $summary->month - 1;
            $incomeData[$idx] = (float) $summary->total_income;
            $expenseData[$idx] = (float) $summary->total_expense;
        }

        $expenseDistribution = [];
        foreach ($categoryTotals as $total) {
            $cat = $categories->firstWhere('id', $total->category_id);
            if ($cat && $cat->type === 'expense') {
                $expenseDistribution[] = [
                    'label' => $cat->name,
                    'value' => (float) $total->total_amount,
                    'color' => $cat->color,
                ];
            }
        }

        return [
            'income_vs_expense' => [
                'labels' => $months,
                'income' => $incomeData,
                'expense' => $expenseData,
            ],
            'expense_distribution' => $expenseDistribution,
            'monthly_cash_flow' => [
                'labels' => $months,
                'data' => array_map(fn ($i, $e) => $i - $e, $incomeData, $expenseData),
            ],
        ];
    }
}
