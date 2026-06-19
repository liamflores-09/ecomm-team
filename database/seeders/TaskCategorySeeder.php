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
            ['department' => 'content', 'column_key' => 'task_1', 'label' => 'New SKU', 'description' => 'Parent / Single product'],
            ['department' => 'content', 'column_key' => 'task_2', 'label' => 'Variation SKU', 'description' => 'Child / Variant'],
            ['department' => 'content', 'column_key' => 'task_3', 'label' => 'Advance Data Gathering', 'description' => 'Research completed'],
            ['department' => 'content', 'column_key' => 'task_4', 'label' => 'Update Listings', 'description' => 'Old SKUs updated'],
            ['department' => 'content', 'column_key' => 'task_5', 'label' => 'Other Tasks', 'description' => 'Canva, etc.'],

            // Lead
            ['department' => 'lead', 'column_key' => 'task_1', 'label' => 'New PR SKU', 'description' => 'New product research'],
            ['department' => 'lead', 'column_key' => 'task_2', 'label' => 'Advance PR', 'description' => 'Advance product research'],
            ['department' => 'lead', 'column_key' => 'task_3', 'label' => 'PR Project', 'description' => 'PR project tasks'],
            ['department' => 'lead', 'column_key' => 'task_4', 'label' => 'JG Used & Trade-in', 'description' => 'JG trade-in tasks'],
            ['department' => 'lead', 'column_key' => 'task_5', 'label' => 'Others', 'description' => 'Other tasks'],

            // Researcher
            ['department' => 'researcher', 'column_key' => 'task_1', 'label' => 'New PR SKU', 'description' => 'New product research'],
            ['department' => 'researcher', 'column_key' => 'task_2', 'label' => 'Advance PR', 'description' => 'Advance product research'],
            ['department' => 'researcher', 'column_key' => 'task_3', 'label' => 'PR Project', 'description' => 'PR project tasks'],
            ['department' => 'researcher', 'column_key' => 'task_4', 'label' => 'JG Used & Trade-in', 'description' => 'JG trade-in tasks'],
            ['department' => 'researcher', 'column_key' => 'task_5', 'label' => 'Others', 'description' => 'Other tasks'],

            // Graphics
            ['department' => 'graphics', 'column_key' => 'task_1', 'label' => 'New CVP', 'description' => 'New content visuals'],
            ['department' => 'graphics', 'column_key' => 'task_2', 'label' => 'Banners', 'description' => 'Banner designs'],
            ['department' => 'graphics', 'column_key' => 'task_3', 'label' => 'Draft', 'description' => 'Draft designs'],
            ['department' => 'graphics', 'column_key' => 'task_4', 'label' => 'Update CVP', 'description' => 'Updated visuals'],
            ['department' => 'graphics', 'column_key' => 'task_5', 'label' => 'Others', 'description' => 'Other tasks'],

            // Backend
            ['department' => 'backend', 'column_key' => 'task_1', 'label' => 'Bulk CP', 'description' => 'Cross listing uploads'],
            ['department' => 'backend', 'column_key' => 'task_2', 'label' => 'Bulk CVP', 'description' => 'Content uploads'],
            ['department' => 'backend', 'column_key' => 'task_3', 'label' => 'Q&A Inquiries', 'description' => 'Q&A responses'],
            ['department' => 'backend', 'column_key' => 'task_4', 'label' => 'QC', 'description' => 'Quality checks'],
            ['department' => 'backend', 'column_key' => 'task_5', 'label' => 'Others', 'description' => 'Other tasks'],
        ];

        foreach ($categories as $cat) {
            TaskCategory::create($cat);
        }
    }
}
