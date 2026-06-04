<?php

namespace App\Http\Controllers;

use App\DTOs\BudgetAllocationDTO;
use App\Http\Requests\StoreBudgetAllocationRequest;
use App\Http\Requests\UpdateBudgetAllocationRequest;
use App\Models\BudgetAllocation;
use App\Services\BudgetAllocationService;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BudgetAllocationController extends Controller
{
    public function __construct(
        private BudgetAllocationService $budgetService,
        private CategoryService $categoryService,
    ) {}

    public function index(Request $request): Response
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $userId = $request->user()->id;

        $allocations = $this->budgetService->getForUserMonth($userId, $month, $year);
        $categories = $this->categoryService->getActiveForUser($userId, 'expense');
        $totalPercentage = $this->budgetService->getTotalPercentage($userId, $month, $year);

        return Inertia::render('BudgetAllocations/Index', [
            'allocations' => $allocations,
            'categories' => $categories,
            'totalPercentage' => $totalPercentage,
            'filters' => [
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    public function store(StoreBudgetAllocationRequest $request): RedirectResponse
    {
        $dto = BudgetAllocationDTO::fromRequest($request->validated());
        $this->budgetService->create($request->user()->id, $dto);

        return redirect()->route('budget-allocations.index', [
            'month' => $dto->month,
            'year' => $dto->year,
        ])->with('success', 'Budget allocation created successfully.');
    }

    public function update(UpdateBudgetAllocationRequest $request, BudgetAllocation $budgetAllocation): RedirectResponse
    {
        $this->authorize('update', $budgetAllocation);

        $dto = BudgetAllocationDTO::fromRequest($request->validated());
        $this->budgetService->update($budgetAllocation, $dto);

        return redirect()->route('budget-allocations.index', [
            'month' => $dto->month,
            'year' => $dto->year,
        ])->with('success', 'Budget allocation updated successfully.');
    }

    public function destroy(BudgetAllocation $budgetAllocation): RedirectResponse
    {
        $this->authorize('delete', $budgetAllocation);

        $month = $budgetAllocation->month;
        $year = $budgetAllocation->year;

        $this->budgetService->delete($budgetAllocation);

        return redirect()->route('budget-allocations.index', [
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Budget allocation deleted successfully.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'allocations' => ['required', 'array'],
            'allocations.*.name' => ['required', 'string', 'max:255'],
            'allocations.*.amount_type' => ['nullable', 'in:percentage,nominal'],
            'allocations.*.amount' => ['required', 'numeric', 'min:0'],
            'allocations.*.color' => ['nullable', 'string'],
        ]);

        $this->budgetService->bulkUpdate(
            $request->user()->id,
            $request->input('month'),
            $request->input('year'),
            $request->input('allocations')
        );

        return redirect()->route('budget-allocations.index', [
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ])->with('success', 'Budget allocations updated successfully.');
    }
}
