<?php

namespace App\Policies;

use App\Models\BudgetAllocation;
use App\Models\User;

class BudgetAllocationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BudgetAllocation $budgetAllocation): bool
    {
        return $user->id === $budgetAllocation->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BudgetAllocation $budgetAllocation): bool
    {
        return $user->id === $budgetAllocation->user_id;
    }

    public function delete(User $user, BudgetAllocation $budgetAllocation): bool
    {
        return $user->id === $budgetAllocation->user_id;
    }
}
