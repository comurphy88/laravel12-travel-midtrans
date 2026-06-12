@extends('layouts.app')

@section('title', $destination->name . ' | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    {{-- Detail Header --}}
    <section class="detail-hero" style="background-image: url('{{ $destination->image }}');">
        <div class="detail-hero__overlay"></div>
        <div class="detail-hero__content" data-aos="fade-up">
            <a href="{{ route('destinations.index') }}" class="detail-back detail-back--hero">
                <i class="ri-arrow-left-line"></i> Kembali ke Destinasi
            </a>
            <span class="hero__badge"><i class="ri-star-fill"></i> {{ $destination->rating }} Rating</span>
            <h1>{{ $destination->name }}</h1>
            <div class="detail-hero__meta">
                <span><i class="ri-map-pin-line"></i> {{ $destination->location }}</span>
                <span><i class="ri-price-tag-3-line"></i> {{ $destination->formatted_price }}</span>
            </div>
        </div>
    </section>

    {{-- Detail Content --}}
    <section class="destinations-section">
        <div class="section__container">
            <div class="detail-grid">
                <div class="detail-main" data-aos="fade-up">
                    <div class="detail-card detail-card--light">
                        <h2><i class="ri-information-line"></i> Tentang Destinasi</h2>
                        <div class="detail-description">
                            <p>{{ $destination->description }}</p>
                        </div>

                        <div class="detail-features">
                            <div class="detail-feature-item detail-feature-item--light">
                                <i class="ri-star-fill"></i>
                                <div>
                                    <strong>Rating</strong>
                                    <span>{{ $destination->rating }} / 5.0</span>
                                </div>
                            </div>
                            <div class="detail-feature-item detail-feature-item--light">
                                <i class="ri-map-pin-2-fill"></i>
                                <div>
                                    <strong>Lokasi</strong>
                                    <span>{{ $destination->location }}</span>
                                </div>
                            </div>
                            <div class="detail-feature-item detail-feature-item--light">
                                <i class="ri-price-tag-3-fill"></i>
                                <div>
                                    <strong>Harga Mulai</strong>
                                    <span>{{ $destination->formatted_price }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-sidebar" data-aos="fade-up" data-aos-delay="100">
                    <div class="detail-card detail-card--light booking-card--light">
                        <h3><i class="ri-ticket-2-line"></i> Pesan ke Destinasi Ini</h3>
                        <div class="booking-price-display">
                            <span class="price-label">Harga mulai dari</span>
                            <span class="price-amount" style="font-size: 1.75rem; display: block; margin: 0.5rem 0;">{{ $destination->formatted_price }}</span>
                            <span class="price-label">per orang</span>
                        </div>
                        <a href="{{ route('bookings.create', ['destination' => $destination->id]) }}" class="btn btn-gold btn-full" style="margin-top: 1.25rem;">
                            <i class="ri-shopping-cart-line"></i> Pesan Sekarang
                        </a>
                        <ul class="booking-features booking-features--light">
                            <li><i class="ri-check-line"></i> Gratis pembatalan</li>
                            <li><i class="ri-check-line"></i> Konfirmasi instan</li>
                            <li><i class="ri-check-line"></i> Layanan bus premium</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Related Destinations --}}
            @if($related->count())
                <div style="margin-top: 4rem;" data-aos="fade-up">
                    <div class="section-header-classic" style="text-align: center;">
                        <span class="section-subtitle">Destinasi Lainnya</span>
                        <h2 class="section__title">Destinasi Wisata Lain</h2>
                        <div class="title-ornament">
                            <span></span>
                            <i class="ri-sparkling-line"></i>
                            <span></span>
                        </div>
                    </div>
                    <div class="destinations-grid">
                        @foreach($related as $rel)
                            <div class="destination-card-elegant">
                                <div class="card-image-wrapper">
                                    <img src="{{ $rel->image }}" alt="{{ $rel->name }}" loading="lazy">
                                    <div class="card-badge"><i class="ri-star-fill"></i> {{ $rel->rating }}</div>
                                    <div class="card-overlay">
                                        <a href="{{ route('destinations.show', $rel) }}" class="btn-overlay">
                                            <i class="ri-eye-line"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                                <div class="card-content-elegant">
                                    <div class="card-location"><i class="ri-map-pin-line"></i> {{ $rel->location }}</div>
                                    <h3>{{ $rel->name }}</h3>
                                    <p>{{ Str::limit($rel->description, 80) }}</p>
                                    <div class="card-footer-elegant">
                                        <div class="price-tag">
                                            <span class="price-label">Mulai</span>
                                            <span class="price-amount">{{ $rel->formatted_price }}</span>
                                        </div>
                                        <a href="{{ route('bookings.create', ['destination' => $rel->id]) }}" class="btn btn-outline-gold">
                                            Pesan Sekarang <i class="ri-arrow-right-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
