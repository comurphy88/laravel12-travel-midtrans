@extends('layouts.app')

@section('title', 'Verifikasi Email | Raven Travel')

@section('body')
    @include('partials.navbar')

    <main class="auth-page">
        <section class="auth-card" data-reveal="fade-up">
            <a href="{{ url('/') }}" class="auth-brand">
                <span class="brand__crest">R</span>
                <span>
                    <strong>Raven Travel</strong>
                    <small>Verifikasi Email</small>
                </span>
            </a>

            <div class="auth-copy">
                <p class="section-subtitle">Satu Langkah Lagi</p>
                <h1>Verifikasi alamat email Anda.</h1>
                <p>Kami telah mengirimkan link verifikasi ke email Anda. Cek folder spam jika tidak menemukannya.</p>
            </div>

            @if(session('status') === 'verification-link-sent')
                <div style="margin-bottom:1rem; padding:12px; background:#d4edda; color:#155724; border-radius:8px; font-size:14px;">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="auth-form" style="margin-bottom:1rem;">
                @csrf
                <button type="submit" class="button button--gold button--large auth-submit">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button button--large auth-submit" style="background:#6c757d; color:#fff; width:100%;">
                    Logout
                </button>
            </form>
        </section>
    </main>
@endsection
