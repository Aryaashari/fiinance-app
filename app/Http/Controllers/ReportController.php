<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index(Request $request): Response
    {
        $type = $request->query('type', 'monthly');
        $month = $request->query('month') ? (int) $request->query('month') : null;
        $year = $request->query('year') ? (int) $request->query('year') : null;
        $quarter = $request->query('quarter') ? (int) $request->query('quarter') : null;

        $reportData = $this->reportService->getReportData(
            $request->user()->id,
            $type,
            $month,
            $year,
            $quarter
        );

        return Inertia::render('Reports/Index', [
            'report' => $reportData,
            'filters' => [
                'type' => $type,
                'month' => $month,
                'year' => $year,
                'quarter' => $quarter,
            ],
        ]);
    }

    public function export(Request $request, string $type, string $format)
    {
        $month = $request->query('month') ? (int) $request->query('month') : null;
        $year = $request->query('year') ? (int) $request->query('year') : null;
        $quarter = $request->query('quarter') ? (int) $request->query('quarter') : null;

        return match ($format) {
            'pdf' => $this->reportService->exportPdf($request->user()->id, $type, $month, $year, $quarter),
            'csv' => $this->reportService->exportCsv($request->user()->id, $type, $month, $year, $quarter),
            default => redirect()->route('reports.index')->with('error', 'Invalid export format.'),
        };
    }
}
