@extends('layouts.app')

@section('title', 'Pesanan Saya | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-ticket-2-line"></i> Riwayat Perjalanan</span>
            <h1>Pesanan <span class="text-gold">Saya</span></h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">

            @if(session('success'))
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="ri-check-line"></i> {{ session('success') }}
                </div>
            @endif

            @if($bookings->isEmpty())
                <div class="empty-state" data-aos="fade-up">
                    <i class="ri-ticket-2-line"></i>
                    <h3>Belum ada pesanan</h3>
                    <p>Anda belum melakukan pemesanan tiket bus. Mulai perjalanan pertama Anda sekarang!</p>
                    <a href="{{ route('bookings.create') }}" class="btn btn-gold">Pesan Tiket Bus</a>
                </div>
            @else
                <div class="bookings-list">
                    @foreach($bookings as $index => $booking)
                        @php
                            $bus = $booking->schedule?->bus;
                        @endphp
                        <div class="booking-card-row" data-aos="fade-up" data-aos-delay="{{ ($index % 5) * 60 }}">

                            {{-- Left: info rute --}}
                            <div class="booking-card-row__main">
                                <div style="display:flex; align-items:center; gap:12px; margin-bottom:0.5rem;">
                                    <div style="width:40px; height:40px; border-radius:10px; background:#fffdf0; border:1px solid #f0e0a0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="ri-bus-line" style="color:#d4a017; font-size:18px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:11px; color:#999;">{{ $booking->booking_code }}</div>
                                        @if($bus)
                                            <h3 style="margin:0; font-size:15px;">{{ $bus->bus_name }}</h3>
                                        @else
                                            <h3 style="margin:0; font-size:15px; color:#999;">Bus tidak tersedia</h3>
                                        @endif
                                    </div>
                                </div>

                                {{-- Rute --}}
                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.5rem;">
                                    <span style="font-size:14px; font-weight:500;">{{ $booking->origin ?? '-' }}</span>
                                    <i class="ri-arrow-right-line" style="color:#d4a017;"></i>
                                    <span style="font-size:14px; font-weight:500;">{{ $booking->destination ?? '-' }}</span>
                                    @if($booking->distance_km)
                                        <span style="font-size:11px; color:#999; margin-left:4px;">· {{ $booking->distance_km }} km</span>
                                    @endif
                                </div>

                                {{-- Meta --}}
                                <div style="display:flex; gap:1rem; flex-wrap:wrap; font-size:12px; color:#999;">
                                    <span><i class="ri-calendar-line"></i> {{ $booking->travel_date->format('d M Y') }}</span>
                                    @if($booking->departure_time)
                                        <span><i class="ri-time-line"></i> {{ substr($booking->departure_time, 0, 5) }}</span>
                                    @endif
                                    <span><i class="ri-group-line"></i> {{ $booking->num_passengers }} penumpang</span>
                                    <span><i class="ri-history-line"></i> {{ $booking->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            {{-- Right: harga & status --}}
                            <div class="booking-card-row__right">
                                <div style="text-align:right; margin-bottom:0.5rem;">
                                    <div style="font-size:11px; color:#999;">Total</div>
                                    <div style="font-size:18px; font-weight:600; color:#d4a017;">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:flex-end; margin-bottom:0.75rem;">
                                    <span class="booking-badge booking-badge--{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <span class="booking-badge booking-badge--pay-{{ $booking->payment_status }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </div>

                                <div style="display:flex; gap:6px; justify-content:flex-end;">
                                    @if($booking->payment_status === 'unpaid' && in_array($booking->status, ['pending', 'confirmed']))
                                        <a href="{{ route('payment.show', $booking) }}" class="btn btn-gold" style="font-size:12px; padding:6px 12px;">
                                            <i class="ri-secure-payment-line"></i> Bayar
                                        </a>
                                    @endif
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-gold" style="font-size:12px; padding:6px 12px;">
                                        Detail
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                @if($bookings->hasPages())
                    <div class="pagination-wrapper" data-aos="fade-up">
                        {{ $bookings->links() }}
                    </div>
                @endif

                <div style="text-align:center; margin-top:2rem;" data-aos="fade-up">
                    <a href="{{ route('bookings.create') }}" class="btn btn-gold">
                        <i class="ri-add-line"></i> Pesan Tiket Baru
                    </a>
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

@push('styles')
    <style>
        .bookings-list { display: flex; flex-direction: column; gap: 12px; }
        .booking-card-row {
            background: var(--surface-2, #fff);
            border: 0.5px solid var(--border, #e5e7eb);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .booking-card-row__main { flex: 1; }
        .booking-card-row__right { flex-shrink: 0; text-align: right; }
        @media (max-width: 640px) {
            .booking-card-row { flex-direction: column; align-items: flex-start; }
            .booking-card-row__right { width: 100%; text-align: left; }
            .booking-card-row__right > div:last-child { justify-content: flex-start !important; }
        }
        .booking-badge {
            display: inline-block; font-size: 11px; font-weight: 500;
            padding: 3px 10px; border-radius: 20px;
        }
        .booking-badge--pending     { background: #fef9c3; color: #854d0e; }
        .booking-badge--confirmed   { background: #dcfce7; color: #166534; }
        .booking-badge--completed   { background: #dbeafe; color: #1e40af; }
        .booking-badge--cancelled   { background: #fee2e2; color: #991b1b; }
        .booking-badge--pay-unpaid  { background: #ffedd5; color: #9a3412; }
        .booking-badge--pay-paid    { background: #dcfce7; color: #166534; }
        .booking-badge--pay-refunded { background: #f3f4f6; color: #374151; }
    </style>
@endpush
