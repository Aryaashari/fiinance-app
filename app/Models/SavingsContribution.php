<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'savings_goal_id',
        'amount',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function savingsGoal(): BelongsTo
    {
        return $this->belongsTo(SavingsGoal::class);
    }
}
