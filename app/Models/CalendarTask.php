<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarTask extends Model
{
    protected $fillable = [
        'parent_id', 'category_id', 'title', 'due_date', 'assigned_role',
        'status', 'description', 'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(CalendarCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subtasks()
    {
        return $this->hasMany(CalendarTask::class, 'parent_id')->orderBy('id');
    }

    public function parent()
    {
        return $this->belongsTo(CalendarTask::class, 'parent_id');
    }
}
