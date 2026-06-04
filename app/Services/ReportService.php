<?php

namespace App\Services;

use App\Repositories\Contracts\BudgetAllocationRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private BudgetAllocationRepositoryInterface $budgetRepository,
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function getReportData(int $userId, string $type, ?int $month = null, ?int $year = null, ?int $quarter = null): array
    {
        $now = Carbon::now();
        $year = $year ?? $now->year;

        switch ($type) {
            case 'monthly':
                $month = $month ?? $now->month;
                return $this->getMonthlyReport($userId, $month, $year);
            case 'quarterly':
                $quarter = $quarter ?? ceil($now->month / 3);
                return $this->getQuarterlyReport($userId, $quarter, $year);
            case 'yearly':
                return $this->getYearlyReport($userId, $year);
            default:
                return $this->getMonthlyReport($userId, $now->month, $year);
        }
    }

    private function getMonthlyReport(int $userId, int $month, int $year): array
    {
        $totals = $this->transactionRepository->getMonthlyTotals($userId, $month, $year);
        $transactions = $this->transactionRepository->getForUserMonth($userId, $month, $year);
        $categoryTotals = $this->transactionRepository->getMonthlyTotalsByCategory($userId, $month, $year);
        $budgetAllocations = $this->budgetRepository->getForUserMonth($userId, $month, $year);
        $categories = $this->categoryRepository->getAllForUser($userId);

        return [
            'type' => 'monthly',
            'period' => Carbon::create($year, $month)->format('F Y'),
            'month' => $month,
            'year' => $year,
            'totals' => $totals,
            'transactions' => $transactions,
            'category_totals' => $categoryTotals->map(function ($item) use ($categories) {
                $cat = $categories->firstWhere('id', $item->category_id);
                return [
                    'category_name' => $cat?->name ?? 'Unknown',
                    'category_type' => $cat?->type ?? 'expense',
                    'category_color' => $cat?->color ?? '#94a3b8',
                    'total_amount' => (float) $item->total_amount,
                ];
            }),
            'budget_allocations' => $budgetAllocations,
        ];
    }

    private function getQuarterlyReport(int $userId, int $quarter, int $year): array
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $startMonth + 2;

        $from = Carbon::create($year, $startMonth, 1)->startOfMonth()->toDateString();
        $to = Carbon::create($year, $endMonth, 1)->endOfMonth()->toDateString();

        $transactions = $this->transactionRepository->getForUserDateRange($userId, $from, $to);
        $categories = $this->categoryRepository->getAllForUser($userId);

        $totalIncome = $transactions->filter(fn ($t) => $t->category->type === 'income')->sum('amount');
        $totalExpense = $transactions->filter(fn ($t) => $t->category->type === 'expense')->sum('amount');

        $monthlySummary = [];
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $monthTotals = $this->transactionRepository->getMonthlyTotals($userId, $m, $year);
            $monthlySummary[] = [
                'month' => Carbon::create($year, $m)->format('F'),
                ...$monthTotals,
            ];
        }

        return [
            'type' => 'quarterly',
            'period' => "Q{$quarter} {$year}",
            'quarter' => $quarter,
            'year' => $year,
            'totals' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'net_cash_flow' => (float) ($totalIncome - $totalExpense),
            ],
            'monthly_summary' => $monthlySummary,
            'transactions' => $transactions,
        ];
    }

    private function getYearlyReport(int $userId, int $year): array
    {
        $from = Carbon::create($year, 1, 1)->startOfYear()->toDateString();
        $to = Carbon::create($year, 12, 31)->endOfYear()->toDateString();

        $transactions = $this->transactionRepository->getForUserDateRange($userId, $from, $to);
        $monthlySummary = $this->transactionRepository->getMonthlySummary($userId, $year);
        $categories = $this->categoryRepository->getAllForUser($userId);

        $totalIncome = $transactions->filter(fn ($t) => $t->category->type === 'income')->sum('amount');
        $totalExpense = $transactions->filter(fn ($t) => $t->category->type === 'expense')->sum('amount');

        return [
            'type' => 'yearly',
            'period' => (string) $year,
            'year' => $year,
            'totals' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'net_cash_flow' => (float) ($totalIncome - $totalExpense),
            ],
            'monthly_summary' => $monthlySummary,
            'transactions' => $transactions,
        ];
    }

    public function exportPdf(int $userId, string $type, ?int $month = null, ?int $year = null, ?int $quarter = null): Response
    {
        $data = $this->getReportData($userId, $type, $month, $year, $quarter);

        $pdf = Pdf::loadView('reports.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "finance-report-{$data['type']}-{$data['period']}.pdf";
        return $pdf->download($filename);
    }

    public function exportCsv(int $userId, string $type, ?int $month = null, ?int $year = null, ?int $quarter = null): StreamedResponse
    {
        $data = $this->getReportData($userId, $type, $month, $year, $quarter);
        $filename = "finance-report-{$data['type']}-{$data['period']}.csv";

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Header
            fputcsv($handle, ['Date', 'Category', 'Type', 'Amount', 'Notes']);

            // Data rows
            foreach ($data['transactions'] as $transaction) {
                fputcsv($handle, [
                    $transaction->transaction_date->format('Y-m-d'),
                    $transaction->category->name,
                    $transaction->category->type,
                    $transaction->amount,
                    $transaction->notes ?? '',
                ]);
            }

            // Summary
            fputcsv($handle, []);
            fputcsv($handle, ['Summary']);
            fputcsv($handle, ['Total Income', $data['totals']['total_income']]);
            fputcsv($handle, ['Total Expense', $data['totals']['total_expense']]);
            fputcsv($handle, ['Net Cash Flow', $data['totals']['net_cash_flow']]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
