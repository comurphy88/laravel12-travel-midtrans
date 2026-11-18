@extends('layouts.app')

@section('title', 'Raven Travel - Perjalanan Elegan & Nyaman')

@section('meta')
    <meta name="description" content="Layanan bus premium dengan sentuhan elegan. Pesan tiket bus antar kota terbaik di Indonesia.">
    <meta name="keywords" content="bus premium, tiket bus, bus antar kota, perjalanan Indonesia, raven travel">
@endsection

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@section('body')
    @include('partials.navbar')

    {{-- Hero Section --}}
    <section id="home" class="hero-classic">
        <div class="ornament ornament-top-left"><i class="ri-leaf-line"></i></div>
        <div class="ornament ornament-bottom-right"><i class="ri-leaf-line"></i></div>

        <div class="hero__container">
            <div class="hero__content" data-aos="fade-up">
                <span class="hero__badge">
                    <i class="ri-vip-crown-line"></i> Pengalaman Perjalanan Premium
                </span>
                <h1 class="hero__title">
                    Perjalanan<br>
                    <span class="text-gold">Elegan & Nyaman</span>
                </h1>
                <p class="hero__description">
                    Mulailah petualangan di mana kemewahan klasik bertemu kenyamanan modern.
                    Nikmati seni perjalanan yang elegan dengan layanan bus premium kami ke berbagai kota di seluruh Indonesia.
                </p>

                <div class="hero__stats">
                    <div class="stat-item">
                        <h3 class="counter" data-target="{{ $stats['routes'] }}">0</h3>
                        <p>Rute Tersedia</p>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <h3>4.9<span class="small">/5</span></h3>
                        <p>Rating Pelanggan</p>
                    </div>
                </div>

                <div class="hero__cta">
                    <a href="{{ route('bookings.create') }}" class="btn btn-gold btn-lg">
                        Pesan Tiket Bus
                        <i class="ri-arrow-right-line"></i>
                    </a>
                    <button class="btn-play" data-video="https://www.youtube.com/embed/dQw4w9WgXcQ">
                        <i class="ri-play-circle-fill"></i>
                        <span>Tonton Cerita Kami</span>
                    </button>
                </div>
            </div>

            <div class="hero__image" data-aos="fade-left" data-aos-delay="200">
                <div class="image-frame">
                    <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=800" alt="Bus Premium" loading="eager">
                    <div class="image-badge">
                        <i class="ri-shield-check-line"></i>
                        <span>Keamanan<br>Tersertifikasi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Gulir untuk Jelajahi</span>
            <i class="ri-arrow-down-line"></i>
        </div>
    </section>

    {{-- Call to Action Section --}}
    <section class="booking-section">
        <div class="section__container">
            <div class="booking-card-elegant" data-aos="zoom-in" style="text-align:center; background:linear-gradient(135deg,#f5f1e8 0%,#f9f7f4 100%);">
                <div class="booking-header" style="justify-content:center; flex-direction:column; border:none;">
                    <i class="ri-bus-line" style="font-size:40px; color:#c9a469; margin-bottom:15px;"></i>
                    <h3 style="margin:0;">Siap untuk Perjalanan Anda?</h3>
                    <p style="color:#999; margin-top:10px; margin-bottom:20px;">Pilih rute bus dan pesan tiket perjalanan Anda sekarang</p>
                </div>
                <a href="{{ route('bookings.create') }}" class="btn btn-gold" style="display:inline-block;">
                    <i class="ri-ticket-2-line"></i> Pesan Tiket Bus
                </a>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="features-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Mengapa Memilih Kami</span>
                <h2 class="section__title">Seni Perjalanan Premium</h2>
                <div class="title-ornament">
                    <span></span><i class="ri-sparkling-line"></i><span></span>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon-wrapper"><i class="ri-vip-crown-fill"></i></div>
                    <h4>Kenyamanan VIP</h4>
                    <p>Kursi reclining mewah dengan ruang kaki ekstra, sistem hiburan pribadi, dan pengaturan suhu.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon-wrapper"><i class="ri-shield-check-fill"></i></div>
                    <h4>Utamakan Keselamatan</h4>
                    <p>Pengemudi tersertifikasi, pelacakan GPS, pemantauan CCTV, dan perawatan kendaraan rutin.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon-wrapper"><i class="ri-wifi-fill"></i></div>
                    <h4>Tetap Terhubung</h4>
                    <p>WiFi berkecepatan tinggi, port USB, dan stopkontak di setiap kursi.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon-wrapper"><i class="ri-restaurant-fill"></i></div>
                    <h4>Layanan Kuliner</h4>
                    <p>Minuman, camilan, dan makanan gratis yang disiapkan oleh tim hospitality di dalam bus.</p>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon-wrapper"><i class="ri-customer-service-2-fill"></i></div>
                    <h4>Dukungan 24/7</h4>
                    <p>Tim support siap membantu sebelum, saat, dan setelah perjalanan Anda.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Schedules Section --}}
    <section id="schedules" class="destinations-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Jadwal Perjalanan</span>
                <h2 class="section__title">Jadwal Bus Tersedia</h2>
                <div class="title-ornament">
                    <span></span><i class="ri-sparkling-line"></i><span></span>
                </div>
                <p class="section__description">
                    Temukan jadwal bus terbaik kami yang menghubungkan berbagai kota di Indonesia
                </p>
            </div>

            <div class="destinations-grid">
                @forelse ($schedules as $index => $schedule)
                    @php $bus = $schedule->bus; $route = $schedule->route; @endphp
                    <div class="destination-card-elegant" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="card-content-elegant" style="padding:1.25rem;">

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                                <div>
                                    <span style="font-size:11px; color:#999;">{{ $bus->type_label ?? 'Bus' }}</span>
                                    <h3 style="margin:0; font-size:15px;">{{ $bus->bus_name ?? '-' }}</h3>
                                </div>
                                <span class="card-badge" style="position:static; font-size:11px;">
                                    {{ $bus->bus_number ?? '' }}
                                </span>
                            </div>

                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:0.75rem;">
                                <div style="text-align:center;">
                                    <div style="font-size:20px; font-weight:600;">{{ substr($schedule->departure_time, 0, 5) }}</div>
                                    <div style="font-size:12px; color:#999;">{{ $route->origin ?? '-' }}</div>
                                </div>
                                <div style="flex:1; text-align:center;">
                                    <div style="border-top:1px dashed #ccc; position:relative;">
                                        <i class="ri-bus-line" style="position:absolute; top:-8px; left:50%; transform:translateX(-50%); background:#fff; padding:0 4px; color:#d4a017;"></i>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <div style="font-size:20px; font-weight:600; color:#999;">-</div>
                                    <div style="font-size:12px; color:#999;">{{ $route->destination ?? '-' }}</div>
                                </div>
                            </div>

                            @if($bus && $bus->facilities)
                                <div style="font-size:11px; color:#999; margin-bottom:0.75rem;">
                                    <i class="ri-checkbox-circle-line"></i> {{ $bus->facilities }}
                                </div>
                            @endif

                            <div style="border-top:1px solid #eee; padding-top:0.75rem; display:flex; justify-content:space-between; align-items:center;">
                                <div class="price-tag">
                                    <span class="price-label">Mulai</span>
                                    <span class="price-amount">
                                        Rp {{ number_format(($bus->price_per_km ?? 0) * ($route->distance_km ?? 0), 0, ',', '.') }}
                                    </span>
                                </div>
                                <a href="{{ route('bookings.create', ['route_id' => $route->id ?? '']) }}" class="btn btn-outline-gold">
                                    Pesan <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="text-center" style="grid-column:1/-1; padding:3rem;">
                        <p>Belum ada jadwal tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="{{ route('bookings.create') }}" class="btn btn-outline-dark btn-lg">
                    Lihat Semua {{ $stats['routes'] }} Rute
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    <section id="services" class="services-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Layanan Kami</span>
                <h2 class="section__title">Layanan Premium, Perjalanan Tenang</h2>
                <div class="title-ornament">
                    <span></span><i class="ri-sparkling-line"></i><span></span>
                </div>
                <p class="section__description">
                    Fasilitas kelas atas untuk memastikan perjalanan Anda aman, nyaman, dan berkesan.
                </p>
            </div>

            <div class="services-grid">
                <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon"><i class="ri-bus-2-fill"></i></div>
                    <h4>Armada Premium</h4>
                    <p>Bus modern dengan kursi reclining, cabin ergonomis, dan ruang kaki ekstra.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-icon"><i class="ri-timer-flash-line"></i></div>
                    <h4>Jadwal Tepat</h4>
                    <p>Keberangkatan tepat waktu dengan monitoring perjalanan real-time.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-icon"><i class="ri-shield-star-line"></i></div>
                    <h4>Keamanan Terjaga</h4>
                    <p>Pengemudi tersertifikasi, CCTV, dan protokol keselamatan ketat.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-icon"><i class="ri-headphone-line"></i></div>
                    <h4>Concierge 24/7</h4>
                    <p>Tim support siap membantu sebelum, saat, dan setelah perjalanan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section id="gallery" class="gallery-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Galeri</span>
                <h2 class="section__title">Momen dalam Perjalanan</h2>
                <div class="title-ornament">
                    <span></span><i class="ri-sparkling-line"></i><span></span>
                </div>
            </div>
            <div class="gallery-grid">
                @foreach ($gallery as $item)
                    <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <img src="{{ $item->image }}" alt="{{ $item->title }}" loading="lazy">
                        <div class="gallery-overlay">{{ $item->title }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    <section id="testimonials" class="testimonials-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Testimoni</span>
                <h2 class="section__title">Kata Para Pelancong Kami</h2>
                <div class="title-ornament">
                    <span></span><i class="ri-sparkling-line"></i><span></span>
                </div>
            </div>
            <div class="swiper testimonials-swiper" data-aos="fade-up" data-aos-delay="200">
                <div class="swiper-wrapper">
                    @foreach ($testimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="quote-mark">&ldquo;</div>
                                <div class="testimonial-content">
                                    <div class="stars">
                                        @for ($i = 0; $i < $testimonial->rating; $i++)
                                            <i class="ri-star-fill"></i>
                                        @endfor
                                    </div>
                                    <p class="testimonial-text">{{ $testimonial->text }}</p>
                                    <div class="testimonial-author">
                                        <img src="{{ $testimonial->image }}" alt="{{ $testimonial->name }}">
                                        <div>
                                            <h5>{{ $testimonial->name }}</h5>
                                            <span>{{ $testimonial->role }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>

    {{-- Video Modal --}}
    <div class="video-modal" id="videoModal" aria-hidden="true">
        <div class="video-modal__content">
            <button class="video-modal__close" id="closeVideo" aria-label="Close video">
                <i class="ri-close-line"></i>
            </button>
            <div class="video-modal__frame">
                <iframe id="videoFrame" title="Raven Travel Story" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    {{-- Newsletter Section --}}
    <section id="newsletter" class="newsletter-classic">
        <div class="section__container">
            <div class="newsletter-content" data-aos="fade-up">
                <div class="newsletter-icon"><i class="ri-mail-line"></i></div>
                <h3>Berlangganan Newsletter Kami</h3>
                <p>Dapatkan penawaran eksklusif, tips perjalanan, dan info rute terbaru langsung ke email Anda</p>
                <form class="newsletter-form" id="newsletterForm" action="{{ route('newsletter.store') }}" method="POST">
                    @csrf
                    <div class="form-group-newsletter">
                        <input type="email" name="email" placeholder="Masukkan alamat email Anda" autocomplete="email" required>
                        <button type="submit" class="btn btn-gold">
                            Berlangganan <i class="ri-send-plane-fill"></i>
                        </button>
                    </div>
                    <small>Kami menghormati privasi Anda. Berhenti berlangganan kapan saja.</small>
                    <div id="newsletterMessage" style="display:none; margin-top:10px; font-size:0.9rem;"></div>
                </form>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endpush
