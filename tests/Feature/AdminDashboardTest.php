<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // TaskLabels::get() falls back to 'content' and recurses infinitely
        // if content has no categories — always seed them.
        $labels = ['New SKU', 'Variation SKU', 'Data Gathering', 'Update Listings', 'Other Tasks'];
        foreach ($labels as $i => $label) {
            TaskCategory::create([
                'department' => 'content',
                'column_key' => 'task_' . ($i + 1),
                'label'      => $label,
            ]);
        }
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    private function makeMember(string $role = 'content'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_top_contributor_shown_only_once(): void
    {
        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->toDateString(),
            'task_1' => 5, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('tc-card');
        // Only the amber KPI card keeps the "Top This Month" label
        $this->assertSame(1, substr_count($response->getContent(), 'Top This Month'));
    }
}
