<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display users with the "user" role
     * and their storage information.
     */
    public function index(): View
    {
        $users = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })
            ->withSum('files as used_bytes', 'size_bytes')
            ->orderBy('username')
            ->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
}