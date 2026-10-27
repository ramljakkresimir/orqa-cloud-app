<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File as FileRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Throwable;

class FileController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', File::class);

        $user = $request->user();

        $isSuperAdmin = $user->hasRole('super_admin');

        if ($isSuperAdmin) {
            // Superadmin can view files uploaded by all users.
            $files = File::with('user')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(5);
        } else {
            // Regular users can view only their own files.
            $files = $user->files()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(5);
        }

        return view('files.index', [
            'files' => $files,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', File::class);

        return view('files.create', [
            'remainingBytes' => $request->user()->remainingStorageBytes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', File::class);

        $validated = $request->validate([
            'document' => [
                'required',
                FileRule::types([
                    'pdf', 'txt',
                    'doc', 'docx',
                    'xls', 'xlsx',
                    'ppt', 'pptx',
                    'jpg', 'jpeg', 'png',
                    'zip',
                ])->extensions([
                    'pdf', 'txt',
                    'doc', 'docx',
                    'xls', 'xlsx',
                    'ppt', 'pptx',
                    'jpg', 'jpeg', 'png',
                    'zip',
                ])
                ->max('20mb'),
            ],
        ]);

        $uploadedFile = $validated['document'];

        $size = (int) $uploadedFile->getSize();
        $storedPath = null;

        try {
            DB::transaction(function () use (
                $request,
                $uploadedFile,
                $size,
                &$storedPath
            ): void {
                // Lock the user row to serialize uploads
                // that belong to the same user.
                $user = User::query()
                    ->lockForUpdate()
                    ->findOrFail($request->user()->id);

                if ($size > $user->remainingStorageBytes()) {
                    throw ValidationException::withMessages([
                        'document' => 'Not enough storage space available.',
                    ]);
                }

                // Store the file under a generated name.
                $storedPath = $uploadedFile->store(
                    'users/'.$user->id,
                    'cloud'
                );

                if ($storedPath === false) {
                    throw new RuntimeException('File storage failed.');
                }

                // Save file metadata to MySQL.
                File::create([
                    'user_id' => $user->id,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'storage_path' => $storedPath,
                    'disk' => 'cloud',
                    'mime_type' => $uploadedFile->getMimeType(),
                    'extension' => strtolower(
                        $uploadedFile->getClientOriginalExtension()
                    ),
                    'size_bytes' => $size,
                ]);
            });
        } catch (Throwable $exception) {
            // Remove the physical file if the database operation fails.
            if ($storedPath !== null && $storedPath !== false) {
                Storage::disk('cloud')->delete($storedPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'File uploaded successfully.');
    }

    private function validateFileStoragePath(File $file): void
    {
        // New uploads use the private cloud disk.
        // Keep legacy local disk support unchanged for now.
        if ($file->disk !== 'cloud') {
            return;
        }

        $expectedDirectory = 'users/'.$file->user_id.'/';

        $path = $file->storage_path;

        abort_unless(
            is_string($path)
            && str_starts_with($path, $expectedDirectory),
            404
        );

        // Only files directly inside the owner's directory
        // are allowed. Reject traversal and nested paths.
        $filename = substr($path, strlen($expectedDirectory));

        abort_unless(
            $filename !== ''
            && $filename !== '.'
            && $filename !== '..'
            && ! str_contains($filename, '/')
            && ! str_contains($filename, '\\')
            && ! str_contains($filename, '..'),
            404
        );
    } 

    public function download(Request $request, File $file): StreamedResponse
    {
        Gate::authorize('download', $file);

        abort_unless($file->disk === 'cloud', 404);

        // Verify that the cloud path belongs to the file owner.
        $this->validateFileStoragePath($file);

        $disk = Storage::disk($file->disk);

        abort_unless(
            $disk->exists($file->storage_path),
            404
        );

        return $disk->download(
            $file->storage_path,
            $file->original_name
        );
    }

    public function destroy(Request $request, File $file): RedirectResponse
    {
        Gate::authorize('delete', $file);

        DB::transaction(function () use ($file): void {
            // Lock the owner row to coordinate with uploads.
            User::query()
                ->lockForUpdate()
                ->findOrFail($file->user_id);

            // Lock and reload the file record.
            $lockedFile = File::query()
                ->lockForUpdate()
                ->findOrFail($file->id);

            // Recheck authorization against the current record.
            Gate::authorize('delete', $lockedFile);

            abort_unless($lockedFile->disk === 'cloud', 404);

            $this->validateFileStoragePath($lockedFile);

            $disk = Storage::disk($lockedFile->disk);

            if ($disk->exists($lockedFile->storage_path)) {
                $deleted = $disk->delete($lockedFile->storage_path);

                if (! $deleted) {
                    throw new RuntimeException(
                        'Unable to delete the stored file.'
                    );
                }
            }

            $lockedFile->delete();
        });

        return redirect()
            ->route('files.index')
            ->with('success', 'File deleted successfully.');
    }
}
