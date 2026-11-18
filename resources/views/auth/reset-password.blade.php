@extends('layouts.app')

@section('title', 'Reset Password | Raven Travel')

@section('body')
    @include('partials.navbar')

    <main class="auth-page">
        <section class="auth-card" data-reveal="fade-up">
            <a href="{{ url('/') }}" class="auth-brand">
                <span class="brand__crest">R</span>
                <span>
                    <strong>Raven Travel</strong>
                    <small>Reset Password</small>
                </span>
            </a>

            <div class="auth-copy">
                <p class="section-subtitle">Buat Password Baru</p>
                <h1>Masukkan password baru Anda.</h1>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:1rem; padding:12px; background:#f8d7da; color:#721c24; border-radius:8px; font-size:14px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <label class="auth-field">
                    <span>Alamat Email</span>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="username">
                    @error('email')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="auth-field">
                    <span>Password Baru</span>
                    <input type="password" name="password" required autocomplete="new-password">
                    @error('password')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="auth-field">
                    <span>Konfirmasi Password</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password">
                </label>

                <button type="submit" class="button button--gold button--large auth-submit">
                    Reset Password
                </button>
            </form>

            <p class="auth-meta">
                Kembali ke <a href="{{ route('login') }}">login</a>
            </p>
        </section>
    </main>
@endsection
