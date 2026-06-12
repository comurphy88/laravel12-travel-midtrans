@extends('layouts.app')

@section('title', 'Masuk | Raven Travel')

@section('body')
        <main class="auth-page">
            <section class="auth-card" data-reveal="fade-up">
                <a href="{{ url('/') }}" class="auth-brand">
                    <span class="brand__crest">R</span>
                    <span>
                        <strong>Raven Travel</strong>
                        <small>Selamat Datang Kembali</small>
                    </span>
                </a>

                <div class="auth-copy">
                    <p class="section-subtitle">Akses Member Pribadi</p>
                    <h1>Masuk untuk melanjutkan perjalanan Anda.</h1>
                    <p>Akses dashboard Anda untuk melihat perjalanan, status booking, dan rencana perjalanan berikutnya.</p>
                </div>

                <form method="post" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <label class="auth-field">
                        <span>Alamat Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="auth-field">
                        <span>Kata Sandi</span>
                        <input type="password" name="password" required autocomplete="current-password">
                        @error('password')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="auth-check">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>

                    <button type="submit" class="button button--gold button--large auth-submit">Masuk</button>
                </form>

                <p class="auth-meta">
                    Belum punya akun?
                    <a href="{{ route('register') }}">Buat akun baru</a>
                </p>
            </section>
        </main>
@endsection