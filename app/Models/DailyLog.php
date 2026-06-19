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
        'task_1',
        'task_2',
        'task_3',
        'task_4',
        'task_5',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'task_1' => 'integer',
            'task_2' => 'integer',
            'task_3' => 'integer',
            'task_4' => 'integer',
            'task_5' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
