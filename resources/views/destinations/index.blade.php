@extends('layouts.app')

@section('title', 'Destinasi Wisata | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    {{-- Page Header --}}
    <section class="page-header">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-compass-3-line"></i> Jelajahi Destinasi</span>
            <h1>Destinasi <span class="text-gold">Wisata</span></h1>
            <p style="color: rgba(255,255,255,0.7); margin-top: 0.5rem;">Temukan berbagai destinasi wisata terbaik di seluruh Indonesia</p>
        </div>
    </section>

    {{-- Destinations Content --}}
    <section class="destinations-section">
        <div class="section__container">

            {{-- Filter Bar --}}
            <form action="{{ route('destinations.index') }}" method="GET" class="destinations-filter" data-aos="fade-up">
                <div class="destinations-filter__search">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari destinasi...">
                </div>
                <div class="destinations-filter__sort">
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Urutkan</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-gold"><i class="ri-search-line"></i> Cari</button>
            </form>

            {{-- Grid --}}
            <div class="destinations-grid">
                @forelse ($destinations as $index => $dest)
                    <div class="destination-card-elegant" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 100 }}">
                        <div class="card-image-wrapper">
                            <img src="{{ $dest->image }}" alt="{{ $dest->name }}" loading="lazy">
                            <div class="card-badge">
                                <i class="ri-star-fill"></i>
                                {{ $dest->rating }}
                            </div>
                            <div class="card-overlay">
                                <a href="{{ route('destinations.show', $dest) }}" class="btn-overlay">
                                    <i class="ri-eye-line"></i>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                        <div class="card-content-elegant">
                            <div class="card-location">
                                <i class="ri-map-pin-line"></i>
                                {{ $dest->location }}
                            </div>
                            <h3>{{ $dest->name }}</h3>
                            <p>{{ Str::limit($dest->description, 100) }}</p>
                            <div class="card-footer-elegant">
                                <div class="price-tag">
                                    <span class="price-label">Mulai</span>
                                    <span class="price-amount">{{ $dest->formatted_price }}</span>
                                </div>
                                <a href="{{ route('bookings.create', ['destination' => $dest->id]) }}" class="btn btn-outline-gold">
                                    Pesan Sekarang <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="ri-map-pin-line"></i>
                        <h3>Tidak ada destinasi ditemukan</h3>
                        <p>Coba ubah kata kunci pencarian Anda.</p>
                        <a href="{{ route('destinations.index') }}" class="btn btn-gold">Atur Ulang Filter</a>
                    </div>
                @endforelse
            </div>

            @if($destinations->hasPages())
                <div class="pagination-wrapper" data-aos="fade-up">
                    {{ $destinations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </section>

    @include('partials.footer')

@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
@endpush
