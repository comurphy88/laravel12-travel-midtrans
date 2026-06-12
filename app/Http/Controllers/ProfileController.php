<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => ['nullable', 'regex:#^(\+62|0)[0-9]{9,12}$#'],
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:50',
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid (contoh: +6281234567890 atau 081234567890)',
        ]);

        // Strip tags from free-text fields. Validation already ran,
        // so this is purely a defence-in-depth measure.
        $validated['name']    = trim(strip_tags($validated['name']));
        $validated['address'] = isset($validated['address']) ? strip_tags($validated['address']) : null;
        $validated['city']    = isset($validated['city'])    ? trim(strip_tags($validated['city'])) : null;

        $user->update($validated);

        return back()->with('success', 'Profile berhasil diupdate.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}