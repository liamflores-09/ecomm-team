<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'nickname'      => 'nullable|string|max:100',
            'mobile_number' => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female',
            'id_number'     => 'nullable|string|max:100',
            'tin'           => 'nullable|string|max:50',
            'sss'           => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:500',
            'password'      => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'nickname'      => $validated['nickname'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'gender'        => $validated['gender'],
            'id_number'     => $validated['id_number'] ?? null,
            'tin'           => $validated['tin'] ?? null,
            'sss'           => $validated['sss'] ?? null,
            'address'       => $validated['address'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048']);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile photo updated.');
    }

    public function removeAvatar()
    {
        $user = Auth::user();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
        return back()->with('success', 'Profile photo removed.');
    }
}
