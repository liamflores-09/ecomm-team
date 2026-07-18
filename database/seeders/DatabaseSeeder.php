<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TaskCategorySeeder::class,
            DailyLogSeeder::class,
            CalendarCategorySeeder::class,
            BrandSeeder::class,
            BrandCatalogSeeder::class,
        ]);
    }
}
