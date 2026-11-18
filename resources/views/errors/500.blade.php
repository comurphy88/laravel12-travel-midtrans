@extends('layouts.app')

@section('title', '500 - Server Error | Raven Travel')

@section('body')
    @include('partials.navbar')

    <section class="section-elegant" style="min-height:70vh; display:flex; align-items:center;">
        <div class="container" style="text-align:center;">
            <div style="font-size:80px; font-weight:700; color:#d4a017; font-family:'Cinzel',serif; line-height:1;">500</div>
            <h2 style="margin:1rem 0 0.5rem; font-size:24px;">Server Error</h2>
            <p style="color:#999; margin-bottom:2rem;">Maaf, terjadi kesalahan pada server. Tim kami sedang memperbaikinya.</p>
            <a href="{{ route('home') }}" class="btn btn-gold">
                <i class="ri-home-line"></i> Kembali ke Beranda
            </a>
        </div>
    </section>

    @include('partials.footer')
@endsection
