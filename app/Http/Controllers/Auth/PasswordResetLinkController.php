<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Generic status message — intentionally identical whether the email
     * exists or not, to avoid user enumeration.
     */
    private const STATUS_MESSAGE = 'Jika email terdaftar, link reset password akan dikirim ke email Anda.';

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->string('email')->toString()));
        $user  = User::where('email', $email)->first();

        // Always return the same response to prevent email enumeration.
        if (! $user) {
            return back()->with('status', self::STATUS_MESSAGE);
        }

        $token = Str::random(64);

        // Replace any existing token for this address.
        PasswordResetToken::where('email', $email)->delete();

        PasswordResetToken::create([
            'email'      => $email,
            'token'      => hash('sha256', $token),
            'created_at' => now(),
        ]);

        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        // TODO: replace with Mail::to($user)->send(new ResetPasswordMail($resetLink))
        // Logging the link is for local dev only — remove before production.
        Log::info("Password reset link for {$email}: {$resetLink}");

        return back()->with('status', self::STATUS_MESSAGE);
    }
}