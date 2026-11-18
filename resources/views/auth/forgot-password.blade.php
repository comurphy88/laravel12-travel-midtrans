@extends('layouts.app')

@section('title', 'Lupa Password | Raven Travel')

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
                <p class="section-subtitle">Lupa Password?</p>
                <h1>Kami akan kirimkan link reset ke email Anda.</h1>
            </div>

            @if(session('status'))
                <div class="alert alert-success" style="margin-bottom:1rem; padding:12px; background:#d4edda; color:#155724; border-radius:8px; font-size:14px;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:1rem; padding:12px; background:#f8d7da; color:#721c24; border-radius:8px; font-size:14px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf
                <label class="auth-field">
                    <span>Alamat Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <button type="submit" class="button button--gold button--large auth-submit">
                    Kirim Link Reset Password
                </button>
            </form>

            <p class="auth-meta">
                Ingat password Anda? <a href="{{ route('login') }}">Login di sini</a>
            </p>
        </section>
    </main>
@endsection
