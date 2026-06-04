<?php

namespace App\Repositories\Eloquent;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllForUser(int $userId, ?string $type = null): Collection
    {
        $query = Category::forUser($userId)->orderBy('name');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function getActiveForUser(int $userId, ?string $type = null): Collection
    {
        $query = Category::forUser($userId)->active()->orderBy('name');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(int $userId, CategoryDTO $dto): Category
    {
        return Category::create([
            'user_id' => $userId,
            ...$dto->toArray(),
        ]);
    }

    public function update(Category $category, CategoryDTO $dto): Category
    {
        $category->update($dto->toArray());
        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function toggleActive(Category $category): Category
    {
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }
}
