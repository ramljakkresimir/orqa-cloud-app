<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'super_admin']);
    }

    public function test_guest_can_register_as_user(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'surname' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'super_admin',
            'quota_bytes' => 999999999999,
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('username', 'testuser')
            ->firstOrFail();

        $this->assertAuthenticatedAs($user);

        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->hasRole('super_admin'));

        $this->assertEquals(
            3221225472,
            $user->quota_bytes
        );
    }

    public function test_duplicate_username_is_rejected(): void
    {
        User::factory()->create([
            'username' => 'taken',
        ]);

        $response = $this->post('/register', [
            'name' => 'New',
            'surname' => 'User',
            'username' => 'taken',
            'email' => 'another@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_regular_user_cannot_access_admin(): void
    {
        $user = User::factory()->create();

        $role = Role::where('name', 'user')
            ->firstOrFail();

        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_superadmin_can_access_admin(): void
    {
        $user = User::factory()->create();

        $role = Role::where('name', 'super_admin')
            ->firstOrFail();

        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }
}
