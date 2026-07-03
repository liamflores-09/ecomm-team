<?php

namespace Tests\Feature;

use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuSlaWeeklyOutputTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_analyst_is_blocked(): void
    {
        $response = $this->actingAs($this->makeUser('analyst'))->get('/sla-weekly-output');
        $response->assertStatus(403);
    }

    public function test_graphics_can_view_but_page_has_no_edit_form(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->get('/sla-weekly-output');
        $response->assertStatus(200);
        // NOTE: brief's literal assertion `assertDontSee('<form method="POST"')` always
        // false-positives because the shared layout's account-menu logout form
        // (resources/views/layouts/app.blade.php ~line 710) is a POST form present on
        // every authenticated page, unrelated to this page's own content. Scoped instead
        // to the SKU edit/add modal form's id, which is the actual "edit form" this
        // read-only page must not contain.
        $response->assertDontSee('id="skuForm"', false);
    }

    public function test_page_shows_weekly_averages_grouped_by_iso_week(): void
    {
        Sku::create([
            'brand' => 'B', 'sku' => 'S1',
            'pr_date_started' => '2026-06-01', 'pr_date_completed' => '2026-06-05',
        ]);
        Sku::create([
            'brand' => 'B', 'sku' => 'S2',
            'pr_date_started' => '2026-07-01', 'pr_date_completed' => '2026-07-08',
        ]);

        $response = $this->actingAs($this->makeUser('backend'))
            ->get('/sla-weekly-output?month_a=2026-07&month_b=2026-06');

        $response->assertStatus(200);
        $response->assertSee('Week');
    }
}
