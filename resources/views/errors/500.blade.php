@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="max-w-md mx-auto">
        <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>
        <h2 class="text-2xl font-semibold text-gray-600 mb-8">Server Error</h2>
        <p class="text-gray-500 mb-8">
            Maaf, terjadi kesalahan pada server. Tim kami telah diberitahu dan sedang memperbaikinya.
        </p>
        <a href="{{ route('home') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection