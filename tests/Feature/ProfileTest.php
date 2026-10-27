<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        // A newly created user must not have files.
        $this->assertSame(0, $user->files()->count());

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
    
    public function test_user_cannot_delete_account_while_files_exist(): void
    {
        Storage::fake('cloud');

        $user = User::factory()->create();

        $path = 'users/'.$user->id.'/document.txt';

        Storage::disk('cloud')->put(
            $path,
            'Test file content'
        );

        $file = File::create([
            'user_id' => $user->id,
            'original_name' => 'document.txt',
            'storage_path' => $path,
            'disk' => 'cloud',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size_bytes' => 17,
        ]);

        $this->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'password',
            ])
            ->assertRedirect('/profile')
            ->assertSessionHasErrors('account');

        // The user must still exist.
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        // File metadata must remain intact.
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'user_id' => $user->id,
        ]);

        // The physical file must still exist.
        Storage::disk('cloud')->assertExists($path);
    }
}
