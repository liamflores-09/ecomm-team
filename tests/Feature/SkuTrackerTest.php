<?php

namespace Tests\Feature;

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

    public function test_add_sku_button_hidden_for_graphics(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->get('/sku-tracker');
        $response->assertDontSee('Add SKU');
    }

    public function test_add_sku_button_visible_for_researcher(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('Add SKU');
    }

    public function test_existing_sku_codes_are_passed_to_view_for_duplicate_check(): void
    {
        \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-DUP-1']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('acme-dup-1');
    }

    public function test_edit_button_hidden_for_graphics(): void
    {
        \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-EDIT-1']);

        $response = $this->actingAs($this->makeUser('graphics'))->get('/sku-tracker');
        $response->assertDontSee('title="Edit"', false);
    }

    public function test_edit_button_visible_for_researcher(): void
    {
        \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-EDIT-2']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('title="Edit"', false);
    }

    public function test_researcher_can_save_pr_file_location_remarks_and_ready_for_cvp(): void
    {
        $sku = \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-PR-FIELDS']);

        $response = $this->actingAs($this->makeUser('researcher'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-PR-FIELDS',
            'pr_file_location' => 'C:\\shared\\pr\\acme-pr-fields.docx',
            'remarks' => 'Waiting on supplier photos.',
            'ready_for_cvp' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', [
            'id' => $sku->id,
            'pr_file_location' => 'C:\\shared\\pr\\acme-pr-fields.docx',
            'remarks' => 'Waiting on supplier photos.',
            'ready_for_cvp' => true,
        ]);
    }

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

    private function makeSku(): \App\Models\Sku
    {
        return \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-EDIT']);
    }

    public function test_researcher_can_edit_pr_fields(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('researcher'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-EDIT',
            'pr_status' => 'DONE',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_status' => 'DONE']);
    }

    public function test_researcher_cannot_edit_content_fields(): void
    {
        $sku = $this->makeSku();

        $this->actingAs($this->makeUser('researcher'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-EDIT',
            'content_assignee' => 'ShouldNotSave',
        ]);

        $this->assertDatabaseMissing('skus', ['id' => $sku->id, 'content_assignee' => 'ShouldNotSave']);
    }

    public function test_content_can_edit_content_fields(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('content'))->put("/sku-tracker/{$sku->id}", [
            'content_assignee' => 'Em',
            'content_date_started' => '2026-07-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'content_assignee' => 'Em']);
    }

    public function test_content_cannot_edit_pr_fields(): void
    {
        $sku = $this->makeSku();

        $this->actingAs($this->makeUser('content'))->put("/sku-tracker/{$sku->id}", [
            'pr_status' => 'DONE',
        ]);

        $this->assertDatabaseMissing('skus', ['id' => $sku->id, 'pr_status' => 'DONE']);
    }

    public function test_graphics_cannot_edit_anything(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('graphics'))->put("/sku-tracker/{$sku->id}", [
            'pr_status' => 'DONE',
        ]);

        $response->assertStatus(403);
    }

    public function test_backend_can_edit_both_sections(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('backend'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-EDIT',
            'pr_status' => 'DONE',
            'content_assignee' => 'Vin',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_status' => 'DONE', 'content_assignee' => 'Vin']);
    }

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

    public function test_month_filter_returns_matching_row(): void
    {
        \App\Models\Sku::create([
            'brand' => 'Acme', 'sku' => 'ACME-MONTH-1',
            'pr_date_started' => '2026-01-05',
        ]);

        $response = $this->actingAs($this->makeUser('researcher'))
            ->get('/sku-tracker?month=2026-01');

        $response->assertStatus(200);
        $response->assertSee('ACME-MONTH-1');
    }

    public function test_edit_payload_serializes_dates_as_plain_ymd(): void
    {
        \App\Models\Sku::create([
            'brand' => 'Acme', 'sku' => 'ACME-DATE-1',
            'pr_date_started' => '2026-01-05',
        ]);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');

        $response->assertStatus(200);
        $response->assertSee('2026-01-05');
        $response->assertDontSee('2026-01-05T', false);
    }

    public function test_table_shows_all_sku_fields_as_columns(): void
    {
        \App\Models\Sku::create([
            'brand' => 'Acme', 'sku' => 'ACME-GRID-1',
            'pr_file_location' => 'C:\path\to\research.xlsx',
            'remarks' => 'Needs re-shoot',
            'ready_for_cvp' => true,
            'cvp_uploaded' => true,
            'shopee_link' => 'https://shopee.ph/product/acme-grid-1',
            'lazada_link' => 'https://lazada.com.ph/products/acme-grid-1',
            'tiktok_link' => 'https://tiktok.com/view/product/acme-grid-1',
            'jg_pro_shopee_link' => 'https://shopee.ph/product/jgpro-acme-1',
            'jg_pro_lazada_link' => 'https://lazada.com.ph/products/jgpro-acme-1',
            'shopify_link' => 'https://jgsuperstore.com/products/acme-grid-1',
            'cinepro_link' => 'https://jgcinepro.com/product/acme-grid-1',
            'lzd_brand_mall_link' => 'https://lazada.com.ph/shop/acme-grid-1',
            'shp_brand_mall_link' => 'https://shopee.ph/shop/acme-grid-1',
            'tt_brand_mall_link' => 'https://tiktok.com/shop/acme-grid-1',
        ]);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertStatus(200);

        // New column headers
        foreach ([
            'PR Date Started', 'PR Date Completed', 'PR File Location', 'Ready for CVP', 'Remarks',
            'Content Date Started', 'Content Date Posted', 'CVP Uploaded',
            'Shopee', 'Lazada', 'TikTok', 'JG PRO Shopee', 'JG PRO Lazada',
            'Shopify', 'CinePro', 'LZD Brand Mall', 'SHP Brand Mall', 'TT Brand Mall',
        ] as $header) {
            $response->assertSee($header);
        }

        // New column values for the row itself
        $response->assertSee('Needs re-shoot');
        $response->assertSee('https://shopee.ph/product/acme-grid-1');
        $response->assertSee('https://tiktok.com/shop/acme-grid-1');
    }

    public function test_brand_and_sku_columns_are_sticky(): void
    {
        \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-STICKY-1']);

        $response = $this->actingAs($this->makeUser('backend'))->get('/sku-tracker');

        $response->assertStatus(200);
        $response->assertSee('sku-col-sticky', false);
    }
}
