@extends('layouts.app')

@section('title', 'Pembayaran - ' . $booking->booking_code . ' | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .pay-container { max-width: 640px; margin: 0 auto; }
        .pay-card { background: var(--card-bg, rgba(255,255,255,0.04)); border: 1px solid var(--border-color, rgba(255,255,255,0.08)); border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; }
        .pay-card h2 { font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--gold); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .pay-summary { display: flex; flex-direction: column; gap: 0.75rem; }
        .pay-row { display: flex; justify-content: space-between; align-items: center; padding: 0.35rem 0; color: var(--text-muted, rgba(255,255,255,0.65)); font-size: 0.95rem; }
        .pay-row strong { color: var(--text-light, #fff); }
        .pay-divider { border: none; border-top: 1px solid var(--border-color, rgba(255,255,255,0.08)); margin: 0.5rem 0; }
        .pay-total { font-size: 1.15rem; font-weight: 700; }
        .pay-total strong { color: var(--gold); font-size: 1.35rem; }
        .btn-pay { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 600; color: #0A0E1A; background: linear-gradient(135deg, var(--gold), #e2c15d); border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s; font-family: 'Lato', sans-serif; }
        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(212,175,55,0.35); }
        .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }
        .pay-secure { text-align: center; margin-top: 1rem; color: var(--text-muted, rgba(255,255,255,0.5)); font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.4rem; }
        .pay-back { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--text-muted); text-decoration: none; margin-bottom: 1.5rem; transition: color 0.2s; }
        .pay-back:hover { color: var(--gold); }
    </style>
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-secure-payment-line"></i> Pembayaran</span>
            <h1>Selesaikan Pembayaran</h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">
            <div class="pay-container">
                <a href="{{ route('bookings.show', $booking) }}" class="pay-back" data-aos="fade-right">
                    <i class="ri-arrow-left-line"></i> Kembali ke Detail Booking
                </a>

                {{-- Order Summary --}}
                <div class="pay-card" data-aos="fade-up">
                    <h2><i class="ri-file-list-3-line"></i> Ringkasan Pesanan</h2>
                    <div class="pay-summary">
                        <div class="pay-row">
                            <span>Kode Booking</span>
                            <strong>{{ $booking->booking_code }}</strong>
                        </div>
                        <div class="pay-row">
                            <span>Perjalanan</span>
                            <strong>
                                @if($booking->busRoute)
                                    {{ $booking->busRoute->route_name }}
                                @elseif($booking->destination)
                                    {{ $booking->destination->name }}
                                @endif
                            </strong>
                        </div>
                        <div class="pay-row">
                            <span>Tanggal</span>
                            <strong>{{ \Carbon\Carbon::parse($booking->travel_date)->format('d F Y') }}</strong>
                        </div>
                        <div class="pay-row">
                            <span>Penumpang</span>
                            <strong>{{ $booking->num_passengers }} orang</strong>
                        </div>
                        <hr class="pay-divider">
                        @php $unitPrice = $booking->num_passengers > 0 ? $booking->total_price / $booking->num_passengers : 0; @endphp
                        <div class="pay-row">
                            <span>Harga per orang</span>
                            <strong>IDR {{ number_format($unitPrice, 0, ',', '.') }}</strong>
                        </div>
                        <div class="pay-row">
                            <span>Jumlah</span>
                            <strong>&times; {{ $booking->num_passengers }}</strong>
                        </div>
                        @if($booking->discount_amount > 0)
                            <div class="pay-row">
                                <span>Diskon</span>
                                <strong style="color: #4ade80;">- IDR {{ number_format($booking->discount_amount, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                        <hr class="pay-divider">
                        <div class="pay-row pay-total">
                            <span>Total Pembayaran</span>
                            <strong>IDR {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Pay Button --}}
                <div data-aos="fade-up" data-aos-delay="100">
                    <button type="button" id="pay-button" class="btn-pay">
                        <i class="ri-secure-payment-line"></i> Bayar Sekarang
                    </button>
                    <p class="pay-secure">
                        <i class="ri-shield-check-line"></i> Pembayaran diproses secara aman oleh Midtrans
                    </p>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        document.getElementById('pay-button').addEventListener('click', function () {
            this.disabled = true;
            this.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Memproses...';

            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = '{{ route('payment.finish', $booking) }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route('payment.finish', $booking) }}';
                },
                onError: function(result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    window.location.reload();
                },
                onClose: function() {
                    document.getElementById('pay-button').disabled = false;
                    document.getElementById('pay-button').innerHTML = '<i class="ri-secure-payment-line"></i> Bayar Sekarang';
                }
            });
        });
    </script>
@endpush
