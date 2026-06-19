<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'attendance',
        'new_sku',
        'variation_sku',
        'advance_data_gathering',
        'update_listings',
        'other_tasks',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'new_sku' => 'integer',
            'variation_sku' => 'integer',
            'advance_data_gathering' => 'integer',
            'update_listings' => 'integer',
            'other_tasks' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getWeekNumberAttribute(): int
    {
        return (int) $this->date->format('W');
    }

    public function getMonthNameAttribute(): string
    {
        return $this->date->format('F');
    }

    public function getQuarterAttribute(): string
    {
        return 'Q' . ceil($this->date->month / 3);
    }
}
