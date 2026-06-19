<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Content
            ['department' => 'content', 'column_key' => 'task_1', 'label' => 'New SKU'],
            ['department' => 'content', 'column_key' => 'task_2', 'label' => 'Variation SKU'],
            ['department' => 'content', 'column_key' => 'task_3', 'label' => 'Advance Data Gathering'],
            ['department' => 'content', 'column_key' => 'task_4', 'label' => 'Update Listings'],
            ['department' => 'content', 'column_key' => 'task_5', 'label' => 'Other Tasks'],

            // Lead
            ['department' => 'lead', 'column_key' => 'task_1', 'label' => 'New PR SKU'],
            ['department' => 'lead', 'column_key' => 'task_2', 'label' => 'Advance PR'],
            ['department' => 'lead', 'column_key' => 'task_3', 'label' => 'PR Project'],
            ['department' => 'lead', 'column_key' => 'task_4', 'label' => 'JG Used & Trade-in'],
            ['department' => 'lead', 'column_key' => 'task_5', 'label' => 'Others'],

            // Researcher
            ['department' => 'researcher', 'column_key' => 'task_1', 'label' => 'New PR SKU'],
            ['department' => 'researcher', 'column_key' => 'task_2', 'label' => 'Advance PR'],
            ['department' => 'researcher', 'column_key' => 'task_3', 'label' => 'PR Project'],
            ['department' => 'researcher', 'column_key' => 'task_4', 'label' => 'JG Used & Trade-in'],
            ['department' => 'researcher', 'column_key' => 'task_5', 'label' => 'Others'],

            // Graphics
            ['department' => 'graphics', 'column_key' => 'task_1', 'label' => 'New CVP'],
            ['department' => 'graphics', 'column_key' => 'task_2', 'label' => 'Banners'],
            ['department' => 'graphics', 'column_key' => 'task_3', 'label' => 'Draft'],
            ['department' => 'graphics', 'column_key' => 'task_4', 'label' => 'Update CVP'],
            ['department' => 'graphics', 'column_key' => 'task_5', 'label' => 'Others'],

            // Backend
            ['department' => 'backend', 'column_key' => 'task_1', 'label' => 'Bulk CP'],
            ['department' => 'backend', 'column_key' => 'task_2', 'label' => 'Bulk CVP'],
            ['department' => 'backend', 'column_key' => 'task_3', 'label' => 'Q&A Inquiries'],
            ['department' => 'backend', 'column_key' => 'task_4', 'label' => 'QC'],
            ['department' => 'backend', 'column_key' => 'task_5', 'label' => 'Others'],
        ];

        foreach ($categories as $cat) {
            TaskCategory::create($cat);
        }
    }
}
