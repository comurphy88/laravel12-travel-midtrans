<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->string('email')));

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'active') {
                return response()->json(
                    ['message' => 'Email sudah terdaftar di newsletter kami.'],
                    JsonResponse::HTTP_CONFLICT
                );
            }

            $subscriber->update([
                'status'           => 'active',
                'subscribed_at'    => now(),
                'unsubscribed_at'  => null,
            ]);

            return response()->json(['message' => 'Berhasil berlangganan kembali!']);
        }

        NewsletterSubscriber::create([
            'email'         => $email,
            'subscribed_at' => now(),
        ]);

        return response()->json(
            ['message' => 'Terima kasih! Anda berhasil berlangganan newsletter.'],
            JsonResponse::HTTP_CREATED
        );
    }
}