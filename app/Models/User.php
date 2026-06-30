<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'nickname',
        'mobile_number',
        'gender',
        'avatar',
        'id_number',
        'tin',
        'tin_hidden',
        'sss',
        'sss_hidden',
        'address',
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
            'password'   => 'hashed',
            'tin_hidden' => 'boolean',
            'sss_hidden' => 'boolean',
        ];
    }

    public function avatarUrl(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        return self::initialsAvatar($this->first_name ?? '', $this->last_name ?? '', $this->username ?? '');
    }

    public static function resolveAvatarUrl(?string $storedPath, string $firstName, string $lastName, string $username = ''): string
    {
        if ($storedPath && Storage::disk('public')->exists($storedPath)) {
            return Storage::url($storedPath);
        }
        return self::initialsAvatar($firstName, $lastName, $username);
    }

    private static function initialsAvatar(string $firstName, string $lastName, string $username = ''): string
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1))
            ?: strtoupper(substr($username, 0, 2));

        $colors = ['#7c3aed','#6366f1','#ec4899','#10b981','#0ea5e9','#f59e0b','#f43f5e','#1e293b'];
        $color  = $colors[abs(crc32($username ?: $firstName)) % count($colors)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">'
            . '<circle cx="20" cy="20" r="20" fill="' . $color . '"/>'
            . '<text x="20" y="26" font-family="sans-serif" font-size="14" font-weight="700" fill="white" text-anchor="middle">' . htmlspecialchars($initials) . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
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
