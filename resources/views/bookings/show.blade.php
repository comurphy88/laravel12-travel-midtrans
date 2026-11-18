@extends('layouts.app')

@section('title', 'Booking #' . $booking->booking_code . ' | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-file-list-3-line"></i> Detail Booking</span>
            <h1>{{ $booking->booking_code }}</h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">
            <a href="{{ route('bookings.index') }}" class="detail-back" data-aos="fade-right">
                <i class="ri-arrow-left-line"></i> Kembali ke Pesanan Saya
            </a>

            @if(session('success'))
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="ri-check-line"></i> {{ session('success') }}
                </div>
            @endif

            @php
                $bus = $booking->schedule?->bus;
            @endphp

            <div class="detail-grid" data-aos="fade-up">
                <div class="detail-main">

                    {{-- Booking Info --}}
                    <div class="detail-card">
                        <h2><i class="ri-information-line"></i> Informasi Booking</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Kode Booking</span>
                                <span class="info-value info-value--code">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Status</span>
                                <span class="booking-badge booking-badge--{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
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
                            <div class="info-item">
                                <span class="info-label">Pembayaran</span>
                                <span class="booking-badge booking-badge--pay-{{ $booking->payment_status }}">{{ ucfirst($booking->payment_status) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Dibuat</span>
                                <span class="info-value">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Trip Info --}}
                    <div class="detail-card">
                        <h2><i class="ri-route-line"></i> Informasi Perjalanan</h2>
                        <div class="trip-card">
                            <div class="trip-journey" style="margin-bottom:1rem;">
                                <div class="route-point">
                                    <span class="route-point__dot"></span>
                                    <div>
                                        <strong>{{ $booking->origin ?? '-' }}</strong>
                                        <small>Kota Asal</small>
                                    </div>
                                </div>
                                <div class="route-line">
                                    <span>{{ $booking->distance_km ? $booking->distance_km . ' km' : '-' }}</span>
                                </div>
                                <div class="route-point">
                                    <span class="route-point__dot route-point__dot--end"></span>
                                    <div>
                                        <strong>{{ $booking->destination ?? '-' }}</strong>
                                        <small>Kota Tujuan</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Bus --}}
                            @if($bus)
                                <div style="border-top:1px solid #eee; padding-top:1rem; display:flex; align-items:center; gap:12px;">
                                    <div style="width:40px; height:40px; border-radius:10px; background:#fffdf0; border:1px solid #f0e0a0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="ri-bus-line" style="color:#d4a017; font-size:18px;"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $bus->bus_name }}</strong>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Passengers --}}
                    @if($booking->passengers->count())
                        <div class="detail-card">
                            <h2><i class="ri-group-line"></i> Data Penumpang</h2>
                            <div class="passenger-list">
                                @foreach($booking->passengers as $i => $passenger)
                                    <div class="passenger-item">
                                        <span class="passenger-num">{{ $i + 1 }}</span>
                                        <div style="flex:1;">
                                            <strong>{{ $passenger->name }}</strong>
                                            @if($passenger->seat_number)
                                                <span style="font-size:12px; color:#d4a017; margin-left:8px;">
                                                    <i class="ri-seat-line"></i> {{ $passenger->seat_number }}
                                                </span>
                                            @endif
                                            @if($passenger->id_number)
                                                <small style="display:block; color:#999;">ID: {{ $passenger->id_number }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Cancellation Policy --}}
                    @if($booking->cancellationPolicy)
                        <div class="detail-card">
                            <h2><i class="ri-refund-2-line"></i> Kebijakan Pembatalan</h2>
                            <p style="color:#666; font-size:14px;">{{ $booking->cancellationPolicy->description }}</p>
                            <p style="font-size:13px; color:#999;">
                                Refund <strong>{{ $booking->cancellationPolicy->refund_percentage }}%</strong>
                                jika dibatalkan lebih dari {{ $booking->cancellationPolicy->hours_before_travel }} jam sebelum keberangkatan.
                            </p>
                        </div>
                    @endif

                    {{-- Notes --}}
                    @if($booking->notes)
                        <div class="detail-card">
                            <h2><i class="ri-sticky-note-line"></i> Catatan</h2>
                            <p style="color:var(--text-muted); font-size:14px;">{{ $booking->notes }}</p>
                        </div>
                    @endif

                </div>

                <div class="detail-sidebar">

                    {{-- Payment Summary --}}
                    <div class="detail-card booking-card">
                        <h3><i class="ri-bill-line"></i> Ringkasan Pembayaran</h3>
                        @php
                            $unitPrice = $booking->num_passengers > 0
                                ? $booking->subtotal / $booking->num_passengers
                                : 0;
                        @endphp
                        <div class="summary-row">
                            <span>Harga per orang</span>
                            <strong>Rp {{ number_format($unitPrice, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Penumpang</span>
                            <strong>× {{ $booking->num_passengers }}</strong>
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
                    </div>

                    {{-- Payment History --}}
                    @if($booking->payments->count())
                        <div class="detail-card">
                            <h3><i class="ri-secure-payment-line"></i> Riwayat Pembayaran</h3>
                            @foreach($booking->payments as $payment)
                                <div class="payment-item">
                                    <div>
                                        <strong>{{ strtoupper($payment->payment_method ?? '-') }}</strong>
                                        <small>{{ $payment->created_at->format('d M Y, H:i') }}</small>
                                        @if($payment->expired_at && $payment->status === 'pending')
                                            <small style="color:#e74c3c; display:block;">
                                                Expired: {{ $payment->expired_at->format('d M Y, H:i') }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="booking-badge booking-badge--{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                                        <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Cetak Tiket --}}
                    @if($booking->payment_status === 'paid')
                        <a href="{{ route('bookings.ticket', $booking) }}"
                           target="_blank"
                           class="btn btn-gold btn-full"
                           style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:1rem;">
                            <i class="ri-printer-line"></i> Cetak Tiket
                        </a>
                    @endif

                    {{-- Pay Button --}}
                    @if($booking->payment_status === 'unpaid' && in_array($booking->status, ['pending', 'confirmed']))
                        <a href="{{ route('payment.show', $booking) }}"
                           class="btn btn-gold btn-full"
                           style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:1rem;">
                            <i class="ri-secure-payment-line"></i> Bayar Sekarang
                        </a>
                    @endif

                    {{-- Cancel Button --}}
                    @if(in_array($booking->status, ['pending', 'confirmed']) && $booking->payment_status !== 'paid')
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-full">
                                <i class="ri-close-circle-line"></i> Batalkan Booking
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
@endpush
