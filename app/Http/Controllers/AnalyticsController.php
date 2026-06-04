<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private TransactionService $transactionService,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $data = $this->dashboardService->getDashboardData($userId);

        // Get yearly summary for analytics
        $year = (int) $request->query('year', now()->year);
        $monthlySummary = $this->transactionService->getMonthlySummary($userId, $year);

        return Inertia::render('Analytics/Index', [
            'dashboardData' => $data,
            'monthlySummary' => $monthlySummary,
            'year' => $year,
        ]);
    }
}
