<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarCategory;

class CalendarCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Deck',                  'color' => '#6366f1'],
            ['name' => 'Meeting Proposals',     'color' => '#10b981'],
            ['name' => 'Appointment Schedules', 'color' => '#f59e0b'],
        ];

        foreach ($categories as $cat) {
            CalendarCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
