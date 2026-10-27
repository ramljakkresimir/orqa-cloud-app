<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user1;
    private User $user2;
    private User $superAdmin;

    protected function setUp(): void
    {

        parent::setUp();

        Storage::fake('cloud');
        Storage::fake('local');

        $userRole = Role::firstOrCreate([
            'name' => 'user',
        ]);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
        ]);

        $this->user1 = User::factory()->create();
        $this->user1->roles()->attach($userRole);

        $this->user2 = User::factory()->create();
        $this->user2->roles()->attach($userRole);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->roles()->attach($superAdminRole);
    }

    private function createTestFile(
        User $user,
        string $name = 'document.txt',
        int $size = 100,
        string $disk = 'cloud'
    ): File {
        $path = 'users/'.$user->id.'/'.Str::uuid().'.txt';

        Storage::disk($disk)->put($path, 'Test file content');

        return File::create([
            'user_id' => $user->id,
            'original_name' => $name,
            'storage_path' => $path,
            'disk' => $disk,
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size_bytes' => $size,
        ]);
    }

    public function test_user_can_upload_a_valid_file(): void
    {
        $uploadedFile = UploadedFile::fake()->create(
            'document.pdf',
            10,
            'application/pdf'
        );

        $response = $this->actingAs($this->user1)
            ->post(route('files.store'), [
                'document' => $uploadedFile,
            ]);

        $response->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('files', [
            'user_id' => $this->user1->id,
            'original_name' => 'document.pdf',
            'disk' => 'cloud',
        ]);

        $file = File::where('user_id', $this->user1->id)
            ->firstOrFail();

        Storage::disk('cloud')->assertExists($file->storage_path);
    }

    public function test_upload_requires_a_file(): void
    {
        $this->actingAs($this->user1)
            ->post(route('files.store'), [])
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('files', 0);
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
        $uploadedFile = UploadedFile::fake()->create(
            'script.php',
            1,
            'application/x-php'
        );

        $this->actingAs($this->user1)
            ->post(route('files.store'), [
                'document' => $uploadedFile,
            ])
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('files', 0);
    }

    public function test_file_larger_than_20_mib_is_rejected(): void
    {
        $uploadedFile = UploadedFile::fake()->create(
            'large.pdf',
            21 * 1024,
            'application/pdf'
        );

        $this->actingAs($this->user1)
            ->post(route('files.store'), [
                'document' => $uploadedFile,
            ])
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('files', 0);
    }

    public function test_user_cannot_exceed_storage_quota(): void
    {
        // Set a small quota to test its boundary.
        $this->user1->update([
            'quota_bytes' => 1024,
        ]);

        $this->createTestFile($this->user1, 'existing.txt', 900);

        $uploadedFile = UploadedFile::fake()->create(
            'new.pdf',
            1,
            'application/pdf'
        );

        $this->actingAs($this->user1)
            ->post(route('files.store'), [
                'document' => $uploadedFile,
            ])
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('files', 1);
    }

    public function test_user_can_download_own_file(): void
    {
        $file = $this->createTestFile($this->user1);

        $this->actingAs($this->user1)
            ->get(route('files.download', $file))
            ->assertOk()
            ->assertDownload('document.txt');
    }

    public function test_user_cannot_download_another_users_file(): void
    {
        $file = $this->createTestFile($this->user2);

        $this->actingAs($this->user1)
            ->get(route('files.download', $file))
            ->assertForbidden();
    }

    public function test_superadmin_can_download_any_file(): void
    {
        $file = $this->createTestFile($this->user2);

        $this->actingAs($this->superAdmin)
            ->get(route('files.download', $file))
            ->assertOk()
            ->assertDownload('document.txt');
    }

    public function test_user_can_delete_own_file(): void
    {
        $file = $this->createTestFile($this->user1);

        $path = $file->storage_path;

        $this->actingAs($this->user1)
            ->delete(route('files.destroy', $file))
            ->assertRedirect(route('files.index'));

        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);

        Storage::disk('cloud')->assertMissing($path);
    }

    public function test_user_cannot_delete_another_users_file(): void
    {
        $file = $this->createTestFile($this->user2);

        $this->actingAs($this->user1)
            ->delete(route('files.destroy', $file))
            ->assertForbidden();

        $this->assertDatabaseHas('files', [
            'id' => $file->id,
        ]);

        Storage::disk('cloud')->assertExists(
            $file->storage_path
        );
    }

    public function test_superadmin_can_delete_any_file(): void
    {
        $file = $this->createTestFile($this->user2);

        $path = $file->storage_path;

        $this->actingAs($this->superAdmin)
            ->delete(route('files.destroy', $file))
            ->assertRedirect(route('files.index'));

        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);

        Storage::disk('cloud')->assertMissing($path);
    }

    public function test_user_sees_only_own_files(): void
    {
        $ownFile = $this->createTestFile(
            $this->user1,
            'my-private-file.txt'
        );

        $otherFile = $this->createTestFile(
            $this->user2,
            'another-user-file.txt'
        );

        $response = $this->actingAs($this->user1)
            ->get(route('files.index'));

        $response->assertOk()
            ->assertSee($ownFile->original_name)
            ->assertDontSee($otherFile->original_name);
    }

    public function test_superadmin_sees_all_files(): void
    {
        $this->createTestFile(
            $this->user1,
            'first-user-file.txt'
        );

        $this->createTestFile(
            $this->user2,
            'second-user-file.txt'
        );

        $this->actingAs($this->superAdmin)
            ->get(route('files.index'))
            ->assertOk()
            ->assertSee('first-user-file.txt')
            ->assertSee('second-user-file.txt')
            ->assertSee('Added By');
    }

    public function test_files_are_paginated_five_per_page(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $this->createTestFile(
                $this->user1,
                "file-{$i}.txt"
            );
        }

        $response = $this->actingAs($this->user1)
            ->get(route('files.index'));

        $response->assertOk();

        $files = $response->viewData('files');

        $this->assertSame(5, $files->count());
        $this->assertSame(7, $files->total());
        $this->assertSame(2, $files->lastPage());

        $response = $this->actingAs($this->user1)
            ->get(route('files.index', ['page' => 2]));

        $response->assertOk();

        $this->assertSame(
            2,
            $response->viewData('files')->count()
        );
    }

    public function test_storage_usage_is_calculated_correctly(): void
    {
        $this->createTestFile($this->user1, 'first.txt', 500);
        $this->createTestFile($this->user1, 'second.txt', 300);

        $this->assertSame(
            800,
            $this->user1->usedStorageBytes()
        );

        $this->assertSame(
            3 * 1024 * 1024 * 1024 - 800,
            $this->user1->remainingStorageBytes()
        );
    }

    public function test_guest_cannot_access_file_management(): void
    {
        $file = $this->createTestFile($this->user1);

        $this->get(route('files.index'))
            ->assertRedirect(route('login'));

        $this->get(route('files.create'))
            ->assertRedirect(route('login'));

        $this->get(route('files.download', $file))
            ->assertRedirect(route('login'));

        $this->delete(route('files.destroy', $file))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_view_admin_user_storage(): void
    {
        $this->actingAs($this->user1)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_superadmin_can_view_user_storage(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($this->user1->username)
            ->assertSee($this->user2->username);
    }
    
    public function test_user_cannot_manipulate_file_metadata_during_upload(): void
    {
        $uploadedFile = UploadedFile::fake()->create(
            'safe.pdf',
            10,
            'application/pdf'
        );

        $this->actingAs($this->user1)
            ->post(route('files.store'), [
                'document' => $uploadedFile,

                // Attempt to override protected metadata.
                'user_id' => $this->user2->id,
                'disk' => 'public',
                'storage_path' => 'malicious/path.pdf',
                'original_name' => 'forged.pdf',
                'size_bytes' => 0,
            ])
            ->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseCount('files', 1);

        $file = File::firstOrFail();

        // The authenticated user must remain the owner.
        $this->assertSame($this->user1->id, $file->user_id);

        // Storage must remain private.
        $this->assertSame('cloud', $file->disk);

        // Client-supplied metadata must be ignored.
        $this->assertSame('safe.pdf', $file->original_name);
        $this->assertSame(10240, $file->size_bytes);

        // Storage path must be generated by Laravel.
        $this->assertStringStartsWith(
            'users/'.$this->user1->id.'/',
            $file->storage_path
        );

        $this->assertNotSame(
            'malicious/path.pdf',
            $file->storage_path
        );

        Storage::disk('cloud')->assertExists(
            $file->storage_path
        );
    }

    public function test_missing_physical_file_cannot_be_downloaded(): void
    {
        $file = $this->createTestFile($this->user1);

        // Simulate a missing file on the storage disk.
        Storage::disk('cloud')->delete(
            $file->storage_path
        );

        $this->actingAs($this->user1)
            ->get(route('files.download', $file))
            ->assertNotFound();

        // Download failure must not modify database metadata.
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'user_id' => $this->user1->id,
        ]);
    }

    public function test_allowed_mime_type_with_forbidden_extension_is_rejected(): void
    {
        $file = UploadedFile::fake()->create(
            'document.php',
            10,
            'application/pdf'
        );

        $this->actingAs($this->user1)
            ->post(route('files.store'), [
                'document' => $file,
            ])
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('files', 0);
    }
}