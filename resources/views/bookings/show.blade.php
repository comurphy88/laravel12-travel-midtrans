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
                <i class="ri-arrow-left-line"></i> Kembali ke Booking Saya
            </a>

            @if(session('success'))
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="ri-check-line"></i> {{ session('success') }}
                </div>
            @endif

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
                                <span class="info-value">{{ \Carbon\Carbon::parse($booking->travel_date)->format('d F Y') }}</span>
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
                        @if($booking->busRoute)
                            <div class="trip-card">
                                <div class="trip-card__header">
                                    <i class="ri-bus-line"></i>
                                    <div>
                                        <h3>{{ $booking->busRoute->route_name }}</h3>
                                        @if($booking->busRoute->bus)
                                            <span>{{ $booking->busRoute->bus->bus_name }} — {{ $booking->busRoute->bus->bus_type }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="trip-journey">
                                    <div class="route-point">
                                        <span class="route-point__dot"></span>
                                        <div>
                                            <strong>{{ $booking->busRoute->origin }}</strong>
                                            <small>{{ $booking->busRoute->departure_time }}</small>
                                        </div>
                                    </div>
                                    <div class="route-line">
                                        <span>{{ $booking->busRoute->duration }} • {{ $booking->busRoute->distance }}</span>
                                    </div>
                                    <div class="route-point">
                                        <span class="route-point__dot route-point__dot--end"></span>
                                        <div>
                                            <strong>{{ $booking->busRoute->destination }}</strong>
                                            <small>{{ $booking->busRoute->arrival_time }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($booking->destination)
                            <div class="trip-card">
                                <div class="trip-card__header">
                                    <i class="ri-map-pin-line"></i>
                                    <div>
                                        <h3>{{ $booking->destination->name }}</h3>
                                        <span>{{ $booking->destination->location }}</span>
                                    </div>
                                </div>
                                @if($booking->destination->description)
                                    <p style="color: var(--text-muted); margin-top: 1rem;">{{ Str::limit($booking->destination->description, 200) }}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Passengers --}}
                    @if($booking->passengers->count())
                        <div class="detail-card">
                            <h2><i class="ri-group-line"></i> Data Penumpang</h2>
                            <div class="passenger-list">
                                @foreach($booking->passengers as $i => $passenger)
                                    <div class="passenger-item">
                                        <span class="passenger-num">{{ $i + 1 }}</span>
                                        <div>
                                            <strong>{{ $passenger->name }}</strong>
                                            @if($passenger->id_number)
                                                <small>ID: {{ $passenger->id_number }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notes --}}
                    @if($booking->notes)
                        <div class="detail-card">
                            <h2><i class="ri-sticky-note-line"></i> Catatan</h2>
                            <p style="color: var(--text-muted);">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="detail-sidebar">
                    {{-- Payment Summary --}}
                    <div class="detail-card booking-card">
                        <h3><i class="ri-bill-line"></i> Ringkasan Pembayaran</h3>
                        <div class="summary-row">
                            <span>Harga per orang</span>
                            @php
                                $unitPrice = $booking->num_passengers > 0 ? $booking->total_price / $booking->num_passengers : 0;
                            @endphp
                            <strong>IDR {{ number_format($unitPrice, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Penumpang</span>
                            <strong>× {{ $booking->num_passengers }}</strong>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <strong class="price-big">IDR {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    {{-- Payments --}}
                    @if($booking->payments->count())
                        <div class="detail-card">
                            <h3><i class="ri-secure-payment-line"></i> Riwayat Pembayaran</h3>
                            @foreach($booking->payments as $payment)
                                <div class="payment-item">
                                    <div>
                                        <strong>{{ strtoupper($payment->payment_method) }}</strong>
                                        <small>{{ $payment->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <div>
                                        <span class="booking-badge booking-badge--{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                                        <strong>IDR {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pay Button --}}
                    @if($booking->payment_status === 'unpaid' && in_array($booking->status, ['pending', 'confirmed']))
                        <a href="{{ route('payment.show', $booking) }}" class="btn btn-gold btn-full" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:1rem;">
                            <i class="ri-secure-payment-line"></i> Bayar Sekarang
                        </a>
                    @endif

                    {{-- Cancel Button --}}
                    @if(in_array($booking->status, ['pending', 'confirmed']))
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
