<?php

namespace App\Support;

use App\Models\TaskCategory;
use Illuminate\Support\Facades\Cache;

class TaskLabels
{
    public static function get(string $role): array
    {
        $cacheKey = 'task_labels_' . $role;

        return Cache::remember($cacheKey, 3600, function () use ($role) {
            $categories = TaskCategory::where('department', $role)
                ->orderBy('column_key')
                ->get();

            $labels = [];
            foreach ($categories as $cat) {
                $labels[$cat->column_key] = $cat->label;
                $labels['desc_' . $cat->column_key] = $cat->description ?? '';
            }

            if (empty($labels)) {
                return self::get('content');
            }

            return $labels;
        });
    }

    public static function clearCache(): void
    {
        $roles = ['content', 'lead', 'researcher', 'graphics', 'backend'];
        foreach ($roles as $role) {
            Cache::forget('task_labels_' . $role);
        }
    }
}
