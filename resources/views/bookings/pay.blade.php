@extends('layouts.app')

@section('title', 'Pembayaran #' . $booking->booking_code . ' | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-secure-payment-line"></i> Pembayaran</span>
            <h1>Selesaikan <span class="text-gold">Pembayaran</span></h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">
            <a href="{{ route('bookings.show', $booking) }}" class="detail-back" data-aos="fade-right">
                <i class="ri-arrow-left-line"></i> Kembali ke Detail Booking
            </a>

            @if(session('success'))
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="ri-check-line"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" data-aos="fade-up">
                    <i class="ri-error-warning-line"></i> {{ session('error') }}
                </div>
            @endif

            @php
                $bus = $booking->schedule?->bus;
            @endphp

            <div class="detail-grid" data-aos="fade-up">
                <div class="detail-main">

                    {{-- Booking Summary --}}
                    <div class="detail-card">
                        <h2><i class="ri-file-list-3-line"></i> Ringkasan Pesanan</h2>

                        {{-- Rute --}}
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:1rem; padding:12px; background:#f9f7f0; border-radius:8px;">
                            <i class="ri-map-pin-line" style="color:#d4a017; font-size:18px;"></i>
                            <span style="font-weight:500;">{{ $booking->origin ?? '-' }}</span>
                            <i class="ri-arrow-right-line" style="color:#d4a017;"></i>
                            <span style="font-weight:500;">{{ $booking->destination ?? '-' }}</span>
                            @if($booking->distance_km)
                                <span style="font-size:12px; color:#999; margin-left:4px;">· {{ $booking->distance_km }} km</span>
                            @endif
                        </div>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Kode Booking</span>
                                <span class="info-value info-value--code">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Bus</span>
                                <span class="info-value">
                                    {{ $bus?->bus_name ?? '-' }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tanggal Perjalanan</span>
                                <span class="info-value">{{ $booking->travel_date->format('d F Y') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jam Berangkat</span>
                                <span class="info-value">{{ $booking->departure_time ? substr($booking->departure_time, 0, 5) . ' WIB' : '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jumlah Penumpang</span>
                                <span class="info-value">{{ $booking->num_passengers }} orang</span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Button --}}
                    <div class="detail-card">
                        <h2><i class="ri-secure-payment-line"></i> Metode Pembayaran</h2>
                        <p style="color:#666; font-size:14px; margin-bottom:1.5rem;">
                            Klik tombol di bawah untuk melanjutkan ke halaman pembayaran Midtrans.
                            Anda dapat memilih berbagai metode pembayaran yang tersedia.
                        </p>

                        @if($booking->payment_status === 'paid')
                            <div class="alert alert-success">
                                <i class="ri-check-double-line"></i> Pembayaran sudah selesai.
                            </div>
                        @else
                            <button id="pay-button" class="btn btn-gold btn-full btn-lg">
                                <i class="ri-secure-payment-line"></i>
                                Bayar Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </button>
                        @endif
                    </div>

                </div>

                <div class="detail-sidebar">
                    <div class="detail-card booking-card" style="position:sticky; top:100px;">
                        <h3><i class="ri-bill-line"></i> Total Pembayaran</h3>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</strong>
                        </div>
                        @if($booking->discount_amount > 0)
                            <div class="summary-row">
                                <span>Diskon</span>
                                <strong style="color:#16a34a;">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <strong class="price-big">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                        </div>

                        @if($booking->payments->where('status', 'pending')->first()?->expired_at)
                            <div style="margin-top:0.75rem; font-size:12px; color:#e74c3c; text-align:center;">
                                <i class="ri-time-line"></i>
                                Bayar sebelum {{ $booking->payments->where('status','pending')->first()->expired_at->format('d M Y, H:i') }}
                            </div>
                        @endif

                        <ul class="booking-features" style="margin-top:1.5rem;">
                            <li><i class="ri-shield-check-line"></i> Pembayaran aman via Midtrans</li>
                            <li><i class="ri-bank-card-line"></i> Transfer, QRIS, e-wallet</li>
                            <li><i class="ri-customer-service-2-line"></i> Support 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @php($snapHost = config('services.midtrans.is_production') ? 'app.midtrans.com' : 'app.sandbox.midtrans.com')
    <script src="https://{{ $snapHost }}/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        @if($booking->payment_token && $booking->payment_status !== 'paid')
        document.getElementById('pay-button')?.addEventListener('click', function () {
            snap.pay('{{ $booking->payment_token }}', {
                onSuccess: function () {
                    window.location.href = '{{ route('payment.finish', $booking) }}';
                },
                onPending: function () {
                    window.location.href = '{{ route('payment.finish', $booking) }}';
                },
                onError: function () {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function () {
                    console.log('Payment popup closed.');
                }
            });
        });
        @endif
    </script>
@endpush
