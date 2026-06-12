<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /** Token lifetime in minutes — must match PasswordResetLinkController. */
    private const TOKEN_TTL_MINUTES = 60;

    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults(), 'min:8'],
            'token'    => ['required'],
        ]);

        $resetToken = PasswordResetToken::where('email', $validated['email'])
            ->where('token', hash('sha256', $validated['token']))
            ->first();

        if (! $resetToken) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['token' => 'Link reset password tidak valid atau sudah ekspirasi.']);
        }

        if ($resetToken->created_at->addMinutes(self::TOKEN_TTL_MINUTES)->isPast()) {
            $resetToken->delete();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['token' => 'Link reset password sudah ekspirasi. Silakan minta link baru.']);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $resetToken->delete();

        ActivityLog::log('password_reset', "Password direset: {$user->email}", 'users', $user->id);

        return redirect()
            ->route('login')
            ->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}