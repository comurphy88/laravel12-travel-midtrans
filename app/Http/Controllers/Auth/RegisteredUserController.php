<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']*$/'],
            'email'    => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults(), 'min:8'],
            'phone'    => ['nullable', 'regex:#^(\+62|0)[0-9]{9,12}$#'],
        ], [
            'name.regex'      => 'Nama hanya boleh mengandung huruf, spasi, dan tanda hubung',
            'email.email'     => 'Format email tidak valid',
            'email.unique'    => 'Email sudah terdaftar',
            'password.min'    => 'Password minimal 8 karakter',
            'phone.regex'     => 'Format nomor telepon tidak valid (contoh: +6281234567890 atau 081234567890)',
        ]);

        // Strip tags from name as a defence-in-depth measure after validation.
        // The 'lowercase' rule on email already guarantees lowercase — no need for strtolower().
        $validated['name'] = trim(strip_tags($validated['name']));

        $user = User::create($validated);

        Auth::login($user);

        // Auth::login() regenerates the session internally;
        // an explicit regenerate() here would be redundant.

        return redirect()->route('bookings.create');
    }
}