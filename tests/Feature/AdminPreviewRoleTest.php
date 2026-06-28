<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPreviewRoleTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    private function makeMember(): User
    {
        return User::factory()->create(['role' => 'content']);
    }

    public function test_admin_can_set_preview_role(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->post(route('admin.preview-role.set'), ['role' => 'content']);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('content', session('preview_role'));
    }

    public function test_admin_can_clear_preview_role(): void
    {
        $admin = $this->makeAdmin();
        session(['preview_role' => 'content']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.preview-role.clear'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertNull(session('preview_role'));
    }

    public function test_invalid_role_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->post(route('admin.preview-role.set'), ['role' => 'hacker']);

        $response->assertSessionHasErrors('role');
    }

    public function test_non_admin_cannot_set_preview_role(): void
    {
        $member = $this->makeMember();
        $response = $this->actingAs($member)
            ->post(route('admin.preview-role.set'), ['role' => 'lead']);

        $response->assertForbidden();
    }
}
