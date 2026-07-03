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
}
