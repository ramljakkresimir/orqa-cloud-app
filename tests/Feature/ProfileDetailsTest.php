<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_name_surname_and_address(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Ana',
            'surname' => 'Horvat',
            'address' => 'Ulica 10, Zagreb',
            'email' => $user->email,
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('Ana', $user->name);
        $this->assertSame('Horvat', $user->surname);
        $this->assertSame('Ulica 10, Zagreb', $user->address);
    }

    public function test_profile_rejects_overlong_surname_and_address(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'surname' => str_repeat('a', 256),
            'address' => str_repeat('b', 256),
            'email' => $user->email,
        ])->assertSessionHasErrors(['surname', 'address']);
    }

    public function test_user_can_change_username_but_not_quota(): void
    {
        $user = User::factory()->create(['username' => 'original-user']);
        $quota = $user->quota_bytes;

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'username' => 'changed-user',
            'quota_bytes' => 999999999999,
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('changed-user', $user->username);
        $this->assertSame($quota, $user->quota_bytes);
    }

    public function test_username_must_be_unique_except_for_current_user(): void
    {
        $user = User::factory()->create(['username' => 'first-user']);
        User::factory()->create(['username' => 'taken-user']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'username' => 'taken-user',
        ])->assertSessionHasErrors('username');

        $this->assertSame('first-user', $user->fresh()->username);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'username' => 'first-user',
        ])->assertSessionHasNoErrors();
    }

    public function test_username_rejects_invalid_format(): void
    {
        $user = User::factory()->create(['username' => 'valid-user']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'username' => 'bad username!',
        ])->assertSessionHasErrors('username');

        $this->assertSame('valid-user', $user->fresh()->username);
    }
}
