<?php

namespace App\Repositories\Contracts;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getAllForUser(int $userId, ?string $type = null): Collection;
    public function getActiveForUser(int $userId, ?string $type = null): Collection;
    public function findById(int $id): ?Category;
    public function create(int $userId, CategoryDTO $dto): Category;
    public function update(Category $category, CategoryDTO $dto): Category;
    public function delete(Category $category): bool;
    public function toggleActive(Category $category): Category;
}
