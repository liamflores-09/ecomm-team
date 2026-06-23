<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'mobile_number' => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female',
            'password'      => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'gender'        => $validated['gender'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);
        return back()->with('success', 'Profile updated successfully.');
    }
}
