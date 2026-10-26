<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Role::firstOrCreate(['name' => 'user']);

        $response = $this->post('/register', [
            'name' => 'Test',
            'surname' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasNoErrors();

        $user = User::where('username', 'testuser')
            ->firstOrFail();

        $this->assertAuthenticatedAs($user);

        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->hasRole('super_admin'));

        $this->assertEquals(
            3221225472,
            $user->quota_bytes
        );

        $response->assertRedirect(
            route('dashboard', absolute: false)
        );
    }
}
