<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarCategory extends Model
{
    protected $fillable = ['name', 'color', 'created_by'];

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
