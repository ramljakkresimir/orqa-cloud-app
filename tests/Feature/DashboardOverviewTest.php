<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_dashboard_displays_only_own_recent_files(): void
    {
        $role = Role::firstOrCreate(['name' => 'user']);
        $user = User::factory()->create(['quota_bytes' => 3221225472]);
        $other = User::factory()->create();
        $user->roles()->attach($role);
        $other->roles()->attach($role);

        File::factory()->create(['user_id' => $user->id, 'original_name' => 'mine.txt']);
        File::factory()->create(['user_id' => $other->id, 'original_name' => 'not-mine.txt']);

        $this->actingAs($user)->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('mine.txt')
            ->assertDontSee('not-mine.txt')
            ->assertSee('3.00 GB');
    }

    public function test_admin_dashboard_displays_all_recent_files_and_owner(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole);
        $owner = User::factory()->create(['username' => 'test_owner']);
        File::factory()->create(['user_id' => $owner->id, 'original_name' => 'report.txt']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('report.txt')
            ->assertSee('test_owner');
    }

    public function test_regular_user_cannot_open_admin_dashboard(): void
    {
        $role = Role::firstOrCreate(['name' => 'user']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }
}
