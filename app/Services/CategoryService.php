<?php

namespace App\Services;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllForUser(int $userId, ?string $type = null): Collection
    {
        return $this->categoryRepository->getAllForUser($userId, $type);
    }

    public function getActiveForUser(int $userId, ?string $type = null): Collection
    {
        return $this->categoryRepository->getActiveForUser($userId, $type);
    }

    public function create(int $userId, CategoryDTO $dto): Category
    {
        return $this->categoryRepository->create($userId, $dto);
    }

    public function update(Category $category, CategoryDTO $dto): Category
    {
        return $this->categoryRepository->update($category, $dto);
    }

    public function delete(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }

    public function toggleActive(Category $category): Category
    {
        return $this->categoryRepository->toggleActive($category);
    }
}
