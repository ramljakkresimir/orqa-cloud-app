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
use RuntimeException;
use Throwable;

class FileController extends Controller
{
    public function index(Request $request): View
    {
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
        return view('files.create', [
            'remainingBytes' => $request->user()->remainingStorageBytes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
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
                ])->max('20mb'),
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
}
