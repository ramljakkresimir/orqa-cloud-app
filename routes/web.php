<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('user')) {
            return redirect()->route('user.dashboard');
        }

        abort(403);
    })->name('dashboard');

    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })
        ->middleware('role:user')
        ->name('user.dashboard');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })
        ->middleware('role:super_admin')
        ->name('admin.dashboard');
});

// Keep Breeze profile routes:
Route::middleware('auth')->group(function () {
    Route::get('/profile', [
        \App\Http\Controllers\ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        \App\Http\Controllers\ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        \App\Http\Controllers\ProfileController::class,
        'destroy'
    ])->name('profile.destroy');
});

// File listing for users and super administrators.
Route::get('/files', [FileController::class, 'index'])
    ->middleware(['auth', 'role:user,super_admin'])
    ->name('files.index');

// Download a file (owner or super administrator).
Route::get('/files/{file}/download', [FileController::class, 'download'])
    ->middleware(['auth', 'role:user,super_admin'])
    ->name('files.download');

// Delete a file (owner or super administrator).
Route::delete('/files/{file}', [FileController::class, 'destroy'])
    ->middleware(['auth', 'role:user,super_admin'])
    ->name('files.destroy');

// File management routes for regular users.
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/files/create', [FileController::class, 'create'])
        ->name('files.create');

    Route::post('/files', [FileController::class, 'store'])
        ->name('files.store');
});

require __DIR__.'/auth.php';