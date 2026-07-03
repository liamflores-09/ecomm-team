<?php

namespace Tests\Feature;

use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuTrackerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makeSku(array $attrs = []): Sku
    {
        return Sku::create(array_merge(['brand' => 'Acme', 'sku' => 'ACME-EDIT'], $attrs));
    }

    // ── Access control ──────────────────────────────────────────

    public function test_analyst_is_blocked_from_sku_tracker(): void
    {
        $response = $this->actingAs($this->makeUser('analyst'))->get('/sku-tracker');
        $response->assertStatus(403);
    }

    public function test_other_roles_can_view_sku_tracker(): void
    {
        foreach (['content', 'researcher', 'graphics', 'backend', 'manager', 'head'] as $role) {
            $response = $this->actingAs($this->makeUser($role))->get('/sku-tracker');
            $response->assertStatus(200);
        }
    }

    public function test_unauthenticated_users_are_redirected(): void
    {
        $response = $this->get('/sku-tracker');
        $response->assertRedirect('/login');
    }

    public function test_add_row_form_hidden_for_graphics(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->get('/sku-tracker');
        $response->assertDontSee('id="addRowForm"', false);
    }

    public function test_add_row_form_visible_for_researcher(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('id="addRowForm"', false);
    }

    public function test_bulk_add_button_hidden_for_content(): void
    {
        $response = $this->actingAs($this->makeUser('content'))->get('/sku-tracker');
        $response->assertDontSee('id="bulkAddModal"', false);
    }

    public function test_bulk_add_button_visible_for_researcher(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('id="bulkAddModal"', false);
    }

    public function test_existing_sku_codes_are_passed_to_view_for_duplicate_check(): void
    {
        $this->makeSku(['sku' => 'ACME-DUP-1']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('acme-dup-1');
    }

    // ── Row creation ────────────────────────────────────────────

    public function test_researcher_can_create_sku(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->post('/sku-tracker', [
            'brand' => 'Acme',
            'sku' => 'ACME-001',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['sku' => 'ACME-001']);
    }

    public function test_content_role_cannot_create_sku(): void
    {
        $response = $this->actingAs($this->makeUser('content'))->post('/sku-tracker', [
            'brand' => 'Acme',
            'sku' => 'ACME-002',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('skus', ['sku' => 'ACME-002']);
    }

    public function test_graphics_cannot_create_sku(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->post('/sku-tracker', [
            'brand' => 'Acme',
            'sku' => 'ACME-003',
        ]);

        $response->assertStatus(403);
    }

    public function test_bulk_add_creates_multiple_skus_from_json(): void
    {
        $payload = json_encode([
            ['brand' => 'Acme', 'sku' => 'BULK-1'],
            ['brand' => 'Acme', 'sku' => 'BULK-2'],
        ]);

        $response = $this->actingAs($this->makeUser('researcher'))->post('/sku-tracker/bulk', [
            'rows_json' => $payload,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['sku' => 'BULK-1']);
        $this->assertDatabaseHas('skus', ['sku' => 'BULK-2']);
    }

    public function test_bulk_add_skips_rows_missing_brand_or_sku(): void
    {
        $payload = json_encode([
            ['brand' => 'Acme', 'sku' => 'BULK-VALID'],
            ['brand' => 'Acme'],
        ]);

        $this->actingAs($this->makeUser('researcher'))->post('/sku-tracker/bulk', [
            'rows_json' => $payload,
        ]);

        $this->assertDatabaseHas('skus', ['sku' => 'BULK-VALID']);
        $this->assertDatabaseCount('skus', 1);
    }

    public function test_bulk_add_forbidden_for_content(): void
    {
        $payload = json_encode([['brand' => 'Acme', 'sku' => 'BULK-DENIED']]);

        $response = $this->actingAs($this->makeUser('content'))->post('/sku-tracker/bulk', [
            'rows_json' => $payload,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('skus', ['sku' => 'BULK-DENIED']);
    }

    // ── Inline field update (auto-save, no form submit) ────────

    public function test_researcher_can_update_a_pr_field_inline(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('researcher'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'pr_status', 'value' => 'Done']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_status' => 'Done']);
    }

    public function test_researcher_cannot_update_a_content_field_inline(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('researcher'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'content_assignee', 'value' => 'ShouldNotSave']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id, 'content_assignee' => 'ShouldNotSave']);
    }

    public function test_content_can_update_a_content_field_inline(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('content'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'content_assignee', 'value' => 'Em']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'content_assignee' => 'Em']);
    }

    public function test_content_cannot_update_a_pr_field_inline(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('content'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'pr_status', 'value' => 'Done']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id, 'pr_status' => 'Done']);
    }

    public function test_graphics_cannot_update_anything_inline(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('graphics'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'pr_status', 'value' => 'Done']);

        $response->assertStatus(403);
    }

    public function test_backend_can_update_both_pr_and_content_fields_inline(): void
    {
        $sku = $this->makeSku();
        $user = $this->makeUser('backend');

        $this->actingAs($user)->putJson("/sku-tracker/{$sku->id}", ['field' => 'pr_status', 'value' => 'Done']);
        $this->actingAs($user)->putJson("/sku-tracker/{$sku->id}", ['field' => 'content_assignee', 'value' => 'Vin']);

        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_status' => 'Done', 'content_assignee' => 'Vin']);
    }

    public function test_update_field_rejects_unknown_field_name(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('backend'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'not_a_real_field', 'value' => 'x']);

        $response->assertStatus(422);
    }

    public function test_update_field_returns_recomputed_values(): void
    {
        $sku = $this->makeSku(['pr_date_completed' => '2026-01-09']);

        $response = $this->actingAs($this->makeUser('content'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'content_date_posted', 'value' => '2026-01-17']);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'computed' => [
                'content_sla' => 8,
                'content_status' => 'DONE',
                'posted' => true,
            ],
        ]);
    }

    public function test_update_field_clears_a_date_when_value_is_empty(): void
    {
        $sku = $this->makeSku(['pr_date_started' => '2026-01-05']);

        $this->actingAs($this->makeUser('researcher'))
            ->putJson("/sku-tracker/{$sku->id}", ['field' => 'pr_date_started', 'value' => '']);

        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_date_started' => null]);
    }

    // ── Navigation ───────────────────────────────────────────────

    public function test_sku_management_nav_visible_to_non_analyst_member(): void
    {
        $response = $this->actingAs($this->makeUser('content'))->get('/dashboard');
        $response->assertSee('SKU Management');
        $response->assertSee('SKU Tracker');
    }

    public function test_sku_management_nav_hidden_from_analyst(): void
    {
        $response = $this->actingAs($this->makeUser('analyst'))->get('/dashboard');
        $response->assertDontSee('SKU Management');
    }

    public function test_sku_management_nav_visible_to_admin(): void
    {
        $response = $this->actingAs($this->makeUser('manager'))->get('/admin');
        $response->assertSee('SKU Management');
    }

    // ── Filters ──────────────────────────────────────────────────

    public function test_month_filter_returns_matching_row(): void
    {
        $this->makeSku(['sku' => 'ACME-MONTH-1', 'pr_date_started' => '2026-01-05']);

        $response = $this->actingAs($this->makeUser('researcher'))
            ->get('/sku-tracker?month=2026-01');

        $response->assertStatus(200);
        $response->assertSee('ACME-MONTH-1');
    }

    public function test_kpi_stats_respect_active_filters(): void
    {
        $this->makeSku(['sku' => 'JAN-1', 'pr_date_started' => '2026-01-05', 'content_date_posted' => '2026-01-10']);
        $this->makeSku(['sku' => 'JUN-1', 'pr_date_started' => '2026-06-05']);
        $this->makeSku(['sku' => 'JUN-2', 'pr_date_started' => '2026-06-06']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker?month=2026-06');

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 2 && $stats['posted'] === 0;
        });
    }

    // ── Date serialization ──────────────────────────────────────

    public function test_dates_render_as_plain_ymd(): void
    {
        $this->makeSku(['sku' => 'ACME-DATE-1', 'pr_date_started' => '2026-01-05']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker?month=');

        $response->assertStatus(200);
        $response->assertSee('2026-01-05');
        $response->assertDontSee('2026-01-05T', false);
    }

    // ── Column set ───────────────────────────────────────────────

    public function test_table_shows_curated_column_set(): void
    {
        $this->makeSku(['sku' => 'ACME-GRID-1', 'remarks' => 'Posted']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertStatus(200);
        foreach ([
            'PR File Location', 'PR Assignee', 'PR Status', 'Ready for CVP', 'Remarks',
            'PR Date Started', 'PR Date Completed', 'PR SLA',
            'Content Assignee', 'Content Status', 'Content Date Started', 'Content Date Posted', 'Content SLA',
            'Posted',
        ] as $header) {
            $response->assertSee($header);
        }
    }

    public function test_table_omits_cvp_uploaded_and_marketplace_link_columns(): void
    {
        $this->makeSku(['sku' => 'ACME-GRID-2']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertDontSee('CVP Uploaded');
        $response->assertDontSee('Shopee');
        $response->assertDontSee('Lazada');
        $response->assertDontSee('JG PRO');
        $response->assertDontSee('Brand Mall');
    }

    public function test_brand_and_sku_columns_are_sticky(): void
    {
        $this->makeSku(['sku' => 'ACME-STICKY-1']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertStatus(200);
        $response->assertSee('sku-col-sticky', false);
    }

    public function test_content_columns_have_distinct_styling_class(): void
    {
        $this->makeSku(['sku' => 'ACME-COLOR-1']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertSee('sku-col-content', false);
    }

    // ── Dropdowns use canonical values ───────────────────────────

    public function test_pr_status_dropdown_uses_canonical_values(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');

        $response->assertSee('In Progress');
        $response->assertSee('>Done<', false);
        $response->assertDontSee('IN PROGRESS');
    }

    public function test_remarks_column_is_a_dropdown_with_canonical_options(): void
    {
        $this->makeSku(['sku' => 'ACME-REMARKS-1']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');

        foreach (['No Resources', 'Out-of-Stock', 'SKU Issue', 'Posted', 'Advance PR', 'Old Posted'] as $option) {
            $response->assertSee($option);
        }
    }

    public function test_assignee_dropdowns_are_populated_from_users(): void
    {
        User::factory()->create(['role' => 'researcher', 'first_name' => 'Milo']);
        User::factory()->create(['role' => 'content', 'first_name' => 'Em']);
        $this->makeSku(['sku' => 'ACME-ASSIGNEE-1']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertSee('Milo');
        $response->assertSee('Em');
    }

    // ── Posted checkbox ──────────────────────────────────────────

    public function test_posted_checkbox_is_checked_when_content_date_posted_is_set(): void
    {
        $this->makeSku(['sku' => 'ACME-POSTED-1', 'content_date_posted' => '2026-01-17']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertSee('checked disabled', false);
    }

    // ── No pagination — show everything ─────────────────────────

    public function test_no_pagination_controls_and_all_matching_rows_render(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            $this->makeSku(['sku' => "PAGE-{$i}"]);
        }

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker?month=');

        $response->assertDontSee('sku-pagination', false);
        for ($i = 1; $i <= 30; $i++) {
            $response->assertSee("PAGE-{$i}");
        }
    }

    // ── Default month filter ─────────────────────────────────────

    public function test_default_view_shows_current_month_and_dateless_rows(): void
    {
        $this->makeSku(['sku' => 'CURRENT-MONTH-1', 'pr_date_started' => now()->format('Y-m') . '-05']);
        $this->makeSku(['sku' => 'NO-DATE-1']);
        $this->makeSku(['sku' => 'OLD-MONTH-1', 'pr_date_started' => now()->subMonths(3)->format('Y-m') . '-05']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertSee('CURRENT-MONTH-1');
        $response->assertSee('NO-DATE-1');
        $response->assertDontSee('OLD-MONTH-1');
    }

    public function test_all_months_option_shows_every_row_including_past_months(): void
    {
        $this->makeSku(['sku' => 'OLD-MONTH-2', 'pr_date_started' => now()->subMonths(5)->format('Y-m') . '-05']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker?month=');

        $response->assertSee('OLD-MONTH-2');
    }

    public function test_month_dropdown_has_all_months_option(): void
    {
        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertSee('All Months');
    }
}
