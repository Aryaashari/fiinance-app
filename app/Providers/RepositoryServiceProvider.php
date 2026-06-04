<?php

namespace App\Providers;

use App\Repositories\Contracts\BudgetAllocationRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Eloquent\BudgetAllocationRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BudgetAllocationRepositoryInterface::class, BudgetAllocationRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
