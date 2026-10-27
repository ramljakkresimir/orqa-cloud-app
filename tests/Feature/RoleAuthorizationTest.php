<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRoles(array $roles): User
    {
        $user = User::factory()->create();

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
            ]);

            $user->roles()->attach($role);
        }

        return $user;
    }

    public function test_user_without_roles_cannot_access_role_protected_routes(): void
    {
        $user = $this->createUserWithRoles([]);

        $this->actingAs($user);

        $this->get(route('dashboard'))->assertForbidden();
        $this->get(route('user.dashboard'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('files.index'))->assertForbidden();
        $this->get(route('files.create'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_role_alone_has_no_file_management_permissions(): void
    {
        $admin = $this->createUserWithRoles(['admin']);

        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertForbidden();
        $this->get(route('user.dashboard'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('files.index'))->assertForbidden();
        $this->get(route('files.create'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_superadmin_cannot_access_upload_routes(): void
    {
        $admin = $this->createUserWithRoles(['super_admin']);

        $this->actingAs($admin);

        $this->get(route('files.create'))->assertForbidden();

        $this->post(route('files.store'), [])
            ->assertForbidden();
    }

    public function test_regular_user_cannot_access_superadmin_routes(): void
    {
        $user = $this->createUserWithRoles(['user']);

        $this->actingAs($user);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_user_with_multiple_roles_receives_combined_permissions(): void
    {
        $user = $this->createUserWithRoles([
            'user',
            'super_admin',
        ]);

        $this->actingAs($user);

        $this->get(route('user.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('files.index'))->assertOk();
        $this->get(route('files.create'))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
    }

    public function test_guest_cannot_access_protected_routes(): void
    {
        $this->get(route('user.dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('files.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }
}