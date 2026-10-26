<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function user(Request $request): View
    {
        $user = $request->user();
        $totalBytes = (int) $user->quota_bytes;
        $usedBytes = $user->usedStorageBytes();

        return view('user.dashboard', [
            'filesCount' => $user->files()->count(),
            'recentFiles' => $user->files()->latest('id')->limit(5)->get(),
            'totalBytes' => $totalBytes,
            'usedBytes' => $usedBytes,
            'remainingBytes' => max(0, $totalBytes - $usedBytes),
            'usedPercentage' => $totalBytes > 0
                ? min(100, round($usedBytes / $totalBytes * 100, 1))
                : 0,
        ]);
    }

    public function admin(): View
    {
        return view('admin.dashboard', [
            'filesCount' => File::count(),
            'usersCount' => User::whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })->count(),
            'usedBytes' => (int) File::sum('size_bytes'),
            'recentFiles' => File::with('user')->latest('id')->limit(5)->get(),
        ]);
    }
}
