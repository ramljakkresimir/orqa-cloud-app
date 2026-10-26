<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    
    public function edit(Request $request): View
    {
        $user = $request->user();

        $totalBytes = (int) $user->quota_bytes;
        $usedBytes = $user->usedStorageBytes();

        $remainingBytes = max(0, $totalBytes - $usedBytes);

        $usedPercentage = $totalBytes > 0
            ? min(100, round(($usedBytes / $totalBytes) * 100, 1))
            : 0;

        return view('profile.edit', [
            'user' => $user,
            'totalBytes' => $totalBytes,
            'usedBytes' => $usedBytes,
            'remainingBytes' => $remainingBytes,
            'usedPercentage' => $usedPercentage,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
