<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandPaletteTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    public function test_admin_sees_admin_pages_in_palette()
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        // Check unique palette description strings that only appear in the JS arrays
        $response->assertSee('Team activity', false);   // Daily Logs desc
        $response->assertSee('Role reports', false);    // Reports desc
        $response->assertSee('User management', false); // Users desc
        $response->assertSee('Manage brands', false);   // Brands desc
    }

    public function test_content_sees_content_pages_in_palette()
    {
        $user = $this->makeUser('content');
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertSee('Product posting guide', false); // Posting Procedure desc
        $response->assertSee('Collect product info', false);  // Data Gathering desc
        $response->assertSee('Platform rules', false);        // Requirements desc
        $response->assertSee('Log daily tasks', false);       // EOD desc
    }

    public function test_analyst_does_not_see_eod_or_calculator_in_palette()
    {
        $user = $this->makeUser('analyst');
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertDontSee('Log daily tasks', false); // EOD desc not in analyst palette
        $response->assertDontSee('Compute SRP', false);     // Calculator desc not in analyst palette
    }

    public function test_admin_does_not_see_member_view_action_in_preview()
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->withSession(['preview_role' => 'content'])
            ->get(route('dashboard'));
        $response->assertDontSee('openMemberView', false);
        $response->assertDontSee('Post an announcement', false);
    }
}
