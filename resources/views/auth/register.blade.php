@extends('layouts.app')

@section('title', 'Daftar | Raven Travel')

@section('body')
        <main class="auth-page">
            <section class="auth-card" data-reveal="fade-up">
                <a href="{{ url('/') }}" class="auth-brand">
                    <span class="brand__crest">R</span>
                    <span>
                        <strong>Raven Travel</strong>
                        <small>Buat Akun Anda</small>
                    </span>
                </a>

                <div class="auth-copy">
                    <p class="section-subtitle">Mulai Keanggotaan Anda</p>
                    <h1>Daftar untuk pengalaman pemesanan yang lebih mudah.</h1>
                    <p>Buat akun untuk menyimpan data perjalanan, melihat riwayat pemesanan, dan mengakses dashboard pribadi Anda.</p>
                </div>

                <form method="post" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <label class="auth-field">
                        <span>Nama Lengkap</span>
                        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
                        @error('name')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="auth-field">
                        <span>Alamat Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="auth-field">
                        <span>Kata Sandi</span>
                        <input type="password" name="password" required autocomplete="new-password">
                        @error('password')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="auth-field">
                        <span>Konfirmasi Kata Sandi</span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password">
                    </label>

                    <button type="submit" class="button button--gold button--large auth-submit">Buat Akun</button>
                </form>

                <p class="auth-meta">
                    Sudah punya akun?
                    <a href="{{ route('login') }}">Login di sini</a>
                </p>
            </section>
        </main>
@endsection