<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'category_id',
        'amount',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('transaction_date', $month)
                     ->whereYear('transaction_date', $year);
    }

    public function scopeForDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->whereHas('category', fn (Builder $q) => $q->where('type', 'income'));
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->whereHas('category', fn (Builder $q) => $q->where('type', 'expense'));
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where($this->getTable() . '.user_id', $userId);
    }

    public function scopeForCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isIncome(): bool
    {
        return $this->category->isIncome();
    }

    public function isExpense(): bool
    {
        return $this->category->isExpense();
    }
}
