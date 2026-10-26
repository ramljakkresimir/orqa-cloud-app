<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    /**
     * Determine whether the user can view the file listing.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('user')
            || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can view a specific file.
     */
    public function view(User $user, File $file): bool
    {
        return $this->canManageFile($user, $file);
    }

    /**
     * Determine whether the user can upload files.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('user');
    }

    /**
     * Determine whether the user can download a file.
     */
    public function download(User $user, File $file): bool
    {
        return $this->canManageFile($user, $file);
    }

    /**
     * Determine whether the user can delete a file.
     */
    public function delete(User $user, File $file): bool
    {
        return $this->canManageFile($user, $file);
    }

    /**
     * Shared permission check for file operations.
     */
    private function canManageFile(User $user, File $file): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasRole('user')
            && (int) $file->user_id === (int) $user->id;
    }
}
