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
            <span class="hero__badge"><i class="ri-history-line"></i> Riwayat</span>
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

            @if($bookings->count())
                <div class="bookings-list" data-aos="fade-up">
                    @foreach($bookings as $booking)
                        <div class="booking-item">
                            <div class="booking-item__header">
                                <div>
                                    <span class="booking-item__code">{{ $booking->booking_code }}</span>
                                    <span class="booking-badge booking-badge--{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                                </div>
                                <span class="booking-item__date">{{ \Carbon\Carbon::parse($booking->travel_date)->format('d M Y') }}</span>
                            </div>
                            <div class="booking-item__body">
                                <div class="booking-item__info">
                                    @if($booking->busRoute)
                                        <h3><i class="ri-bus-line"></i> {{ $booking->busRoute->route_name }}</h3>
                                        <p>{{ $booking->busRoute->origin }} → {{ $booking->busRoute->destination }}</p>
                                    @elseif($booking->destination)
                                        <h3><i class="ri-map-pin-line"></i> {{ $booking->destination->name }}</h3>
                                        <p>{{ $booking->destination->location }}</p>
                                    @endif
                                    <span class="booking-item__pax"><i class="ri-user-line"></i> {{ $booking->num_passengers }} penumpang</span>
                                </div>
                                <div class="booking-item__right">
                                    <div class="booking-item__price">IDR {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                    <span class="booking-badge booking-badge--pay-{{ $booking->payment_status }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-gold btn-sm">
                                        <i class="ri-eye-line"></i> Detail
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
            @else
                <div class="empty-state" data-aos="fade-up">
                    <i class="ri-inbox-line"></i>
                    <h3>Belum ada booking</h3>
                    <p>Anda belum melakukan pemesanan apapun.</p>
                    <a href="{{ route('destinations.index') }}" class="btn btn-gold">Jelajahi Destinasi</a>
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
