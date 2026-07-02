<?php

namespace Tests\Feature;

use App\Models\Attendance;
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

    public function test_trend_data_covers_30_days_and_zero_fills(): void
    {
        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->toDateString(),
            'task_1' => 3, 'task_2' => 2, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->subDays(10)->toDateString(),
            'task_1' => 4, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $trendData = $response->viewData('trendData');
        $this->assertCount(30, $trendData);
        $this->assertSame(5, $trendData[29]);          // today = index 29
        $this->assertSame(4, $trendData[19]);          // 10 days ago
        $this->assertSame(0, $trendData[0]);           // zero-filled
        $this->assertCount(30, $response->viewData('trendLabels'));
        $this->assertSame(now()->format('M j'), $response->viewData('trendLabels')[29]);
        $this->assertSame(array_slice($trendData, -7), $response->viewData('sparkData'));
    }

    public function test_trend_chart_and_toggle_render(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('id="trendChart"', false);
        $response->assertSee('data-days="7"', false);
        $response->assertSee('data-days="30"', false);
    }

    public function test_task_type_breakdown_groups_this_month_by_role(): void
    {
        $admin    = $this->makeAdmin();
        $content  = $this->makeMember();
        $graphics = $this->makeMember('graphics');
        TaskCategory::create(['department' => 'graphics', 'column_key' => 'task_1', 'label' => 'Banners']);
        TaskCategory::create(['department' => 'graphics', 'column_key' => 'task_2', 'label' => 'Thumbnails']);

        DailyLog::create([
            'user_id' => $content->id, 'date' => now()->toDateString(),
            'task_1' => 5, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);
        DailyLog::create([
            'user_id' => $graphics->id, 'date' => now()->toDateString(),
            'task_1' => 0, 'task_2' => 7, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);
        DailyLog::create([ // last day of previous month — excluded (avoid subMonth() overflow)
            'user_id' => $content->id, 'date' => now()->startOfMonth()->subDay()->toDateString(),
            'task_1' => 99, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $bd = $response->viewData('taskTypeBreakdown');
        $this->assertSame(5, $bd['content']['data'][0]);
        $this->assertSame('New SKU', $bd['content']['labels'][0]);
        $this->assertSame(7, $bd['graphics']['data'][1]);
        $this->assertSame('Banners', $bd['graphics']['labels'][0]);
        // no researcher logs -> zeroed data, labels fall back to content's
        $this->assertSame([0, 0, 0, 0, 0], $bd['researcher']['data']);
        $this->assertSame('New SKU', $bd['researcher']['labels'][0]);
    }

    public function test_task_type_chart_and_role_pills_render(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('id="taskTypeChart"', false);
        $response->assertSee('data-role="content"', false);
        $response->assertSee('data-role="graphics"', false);
        $response->assertSee('data-role="backend"', false);
        $response->assertSee('data-role="researcher"', false);
    }

    public function test_attendance_week_counts_and_out_today(): void
    {
        // Freeze to a Wednesday so the Mon–Sat window is deterministic
        $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0));

        $admin    = $this->makeAdmin();
        $content  = $this->makeMember();
        $graphics = $this->makeMember('graphics');

        Attendance::create(['user_id' => $content->id,  'date' => now()->toDateString(),           'status' => 'present']);
        Attendance::create(['user_id' => $graphics->id, 'date' => now()->toDateString(),           'status' => 'sl']);
        Attendance::create(['user_id' => $content->id,  'date' => now()->subDay()->toDateString(), 'status' => 'absent']);
        Attendance::create(['user_id' => $content->id,  'date' => now()->subWeek()->toDateString(),'status' => 'absent']);  // outside window
        Attendance::create(['user_id' => $admin->id,    'date' => now()->toDateString(),           'status' => 'present']); // manager excluded

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $counts = $response->viewData('attWeekCounts');
        $this->assertSame(1, $counts['present']);
        $this->assertSame(1, $counts['sl']);
        $this->assertSame(1, $counts['absent']);
        $this->assertSame(0, $counts['vl']);

        $outToday = $response->viewData('outToday');
        $this->assertCount(1, $outToday);
        $this->assertTrue($outToday->first()->is($graphics));
    }

    public function test_attendance_card_renders_with_out_today(): void
    {
        $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0)); // Wednesday

        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        Attendance::create(['user_id' => $member->id, 'date' => now()->toDateString(), 'status' => 'vl']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Attendance This Week');
        $response->assertSee('Out today');
    }

    public function test_attendance_card_empty_state(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('No attendance marked yet this week');
        $response->assertDontSee('Out today');
    }
}
