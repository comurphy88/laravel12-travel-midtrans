@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-box">
        <h2>Verifikasi Email</h2>
        <p class="auth-subtitle">Kami telah mengirimkan link verifikasi ke email Anda</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                Link verifikasi baru telah dikirim ke email Anda.
            </div>
        @endif

        <p class="verification-info">
            Silakan cek email Anda dan klik link verifikasi untuk melanjutkan. 
            Jika Anda tidak menerima email dalam beberapa menit, silakan periksa folder spam.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="btn btn-primary btn-block">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <hr class="divider">

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-secondary btn-block">
                Logout
            </button>
        </form>
    </div>
</div>

<style>
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .auth-box {
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        padding: 40px;
        width: 100%;
        max-width: 400px;
    }

    .auth-box h2 {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
    }

    .auth-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .verification-info {
        text-align: center;
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .btn {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .divider {
        margin: 20px 0;
        border: none;
        border-top: 1px solid #eee;
    }

    .alert {
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>
@endsection
