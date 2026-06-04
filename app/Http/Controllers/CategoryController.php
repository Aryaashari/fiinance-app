<?php

namespace App\Http\Controllers;

use App\DTOs\CategoryDTO;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(Request $request): Response
    {
        $type = $request->query('type');
        $categories = $this->categoryService->getAllForUser($request->user()->id, $type);

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => [
                'type' => $type,
            ],
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $dto = CategoryDTO::fromRequest($request->validated());
        $this->categoryService->create($request->user()->id, $dto);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $dto = CategoryDTO::fromRequest($request->validated());
        $this->categoryService->update($category, $dto);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $this->categoryService->delete($category);

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $this->categoryService->toggleActive($category);

        $status = $category->fresh()->is_active ? 'activated' : 'deactivated';

        return redirect()->route('categories.index')
            ->with('success', "Category {$status} successfully.");
    }
}
