<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\BrandCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_all_authenticated_users_can_view_brand_catalogs(): void
    {
        foreach (['content', 'lead', 'researcher', 'graphics', 'backend', 'manager'] as $role) {
            $response = $this->actingAs($this->makeUser($role))->get('/brand-catalogs');
            $response->assertStatus(200);
        }
    }

    public function test_unauthenticated_users_are_redirected(): void
    {
        $response = $this->get('/brand-catalogs');
        $response->assertRedirect('/login');
    }

    public function test_researcher_can_create_catalog(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        $user = $this->makeUser('researcher');

        $response = $this->actingAs($user)->post('/brand-catalogs', [
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
            'link'     => 'https://example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('brand_catalogs', ['title' => 'Test Catalog']);
    }

    public function test_content_role_cannot_create_catalog(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        $user = $this->makeUser('content');

        $response = $this->actingAs($user)->post('/brand-catalogs', [
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
            'link'     => 'https://example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_catalog_requires_link_or_file(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        $user = $this->makeUser('manager');

        $response = $this->actingAs($user)->post('/brand-catalogs', [
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('brand_catalogs', ['title' => 'Test Catalog']);
    }

    public function test_admin_can_create_brand(): void
    {
        $user = $this->makeUser('manager');

        $response = $this->actingAs($user)->post('/admin/brands', [
            'name' => 'Samsung',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('brands', ['name' => 'Samsung']);
    }

    public function test_brand_delete_blocked_when_catalogs_exist(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        BrandCatalog::create([
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
            'link'     => 'https://example.com',
        ]);

        $user = $this->makeUser('manager');
        $response = $this->actingAs($user)->delete('/admin/brands/' . $brand->id);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('brands', ['id' => $brand->id]);
    }
}
