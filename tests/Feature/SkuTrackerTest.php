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
}
