<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'mobile_number',
        'gender',
        'badge',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['manager', 'head']);
    }

    public function isHead(): bool
    {
        return $this->role === 'head';
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getInitialsAttribute(): string
    {
        $name = $this->full_name;
        if ($name) {
            $parts = explode(' ', $name);
            return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
        }
        return strtoupper(substr($this->username, 0, 2));
    }

    public function getDepartmentAttribute(): string
    {
        return match ($this->role) {
            'content' => 'Content',
            'graphics' => 'Graphics',
            'lead' => 'PR',
            'head' => 'Ecomm Head',
            default => ucfirst($this->role),
        };
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function buddies(): HasMany
    {
        return $this->hasMany(Schedule::class, 'buddy_id');
    }
}
