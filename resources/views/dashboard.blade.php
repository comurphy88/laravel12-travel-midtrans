@extends('layouts.app')

@section('title', 'Dasbor | Raven Travel')

@section('body')
        <div class="dash">
            <aside class="dash-sidebar">
                <a href="{{ url('/') }}" class="brand">
                    <span class="brand__crest">R</span>
                    <span>
                        <strong>Raven Travel</strong>
                        <small>Area Member</small>
                    </span>
                </a>

                <nav class="dash-nav">
                    <a href="{{ route('dashboard') }}" class="dash-nav__link dash-nav__link--active">
                        <i class="ri-dashboard-3-line"></i>
                        <span>Dasbor</span>
                    </a>
                    <a href="{{ route('bookings.index') }}" class="dash-nav__link">
                        <i class="ri-ticket-2-line"></i>
                        <span>Pesanan Saya</span>
                    </a>
                    <a href="{{ route('bookings.create') }}" class="dash-nav__link">
                        <i class="ri-calendar-check-line"></i>
                        <span>Pesan Perjalanan</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="dash-nav__link">
                        <i class="ri-settings-4-line"></i>
                        <span>Pengaturan</span>
                    </a>
                </nav>

                <div class="dash-sidebar__bottom">
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dash-nav__link dash-nav__link--logout">
                            <i class="ri-logout-box-r-line"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="dash-main">
                <header class="dash-topbar">
                    <div>
                        <p class="section-subtitle">Dasbor Member</p>
                        <h1>Selamat datang, {{ auth()->user()->name }}.</h1>
                    </div>
                    <div class="dash-topbar__right">
                        <div class="dash-avatar">
                            <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                </header>

                <div class="dash-stats">
                    <article class="dash-stat-card" data-reveal="fade-up">
                        <div class="dash-stat-card__icon dash-stat-card__icon--gold">
                            <i class="ri-route-line"></i>
                        </div>
                        <div>
                            <span class="dash-stat-card__label">Total Perjalanan</span>
                            <strong class="dash-stat-card__value">{{ $totalTrips }}</strong>
                        </div>
                    </article>
                    <article class="dash-stat-card" data-reveal="fade-up" data-reveal-delay="60">
                        <div class="dash-stat-card__icon dash-stat-card__icon--green">
                            <i class="ri-ticket-2-line"></i>
                        </div>
                        <div>
                            <span class="dash-stat-card__label">Tiket Aktif</span>
                            <strong class="dash-stat-card__value">{{ $activeTickets }}</strong>
                        </div>
                    </article>
                </div>

                <div class="dash-content-grid">
                    <section class="dash-card dash-card--main" data-reveal="fade-up">
                        <div class="dash-card__header">
                            <h3><i class="ri-compass-3-line"></i> Aksi Cepat</h3>
                        </div>
                        <div class="dash-quick-actions">
                            <a href="{{ route('destinations.index') }}" class="dash-action-tile">
                                <div class="dash-action-tile__icon"><i class="ri-map-pin-line"></i></div>
                                <strong>Jelajahi Destinasi</strong>
                                <p>Temukan rute premium di seluruh Indonesia</p>
                            </a>
                            <a href="{{ route('bookings.create') }}" class="dash-action-tile">
                                <div class="dash-action-tile__icon"><i class="ri-calendar-check-line"></i></div>
                                <strong>Pesan Perjalanan</strong>
                                <p>Reservasi kursi untuk perjalanan berikutnya</p>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="dash-action-tile">
                                <div class="dash-action-tile__icon"><i class="ri-user-settings-line"></i></div>
                                <strong>Edit Profil</strong>
                                <p>Perbarui pengaturan akun Anda</p>
                            </a>
                        </div>
                    </section>

                    <section class="dash-card" data-reveal="fade-up" data-reveal-delay="80">
                        <div class="dash-card__header">
                            <h3><i class="ri-user-line"></i> Ringkasan Profil</h3>
                        </div>
                        <div class="dash-profile-list">
                            <div class="dash-profile-item">
                                <span>Nama Lengkap</span>
                                <strong>{{ auth()->user()->name }}</strong>
                            </div>
                            <div class="dash-profile-item">
                                <span>Email</span>
                                <strong>{{ auth()->user()->email }}</strong>
                            </div>
                            <div class="dash-profile-item">
                                <span>Member Sejak</span>
                                <strong>{{ auth()->user()->created_at?->format('d M Y') }}</strong>
                            </div>
                            <div class="dash-profile-item">
                                <span>Tingkat Member</span>
                                <strong class="text-gold">Klasik</strong>
                            </div>
                        </div>
                    </section>
                </div>

                <section class="dash-card" data-reveal="fade-up">
                    <div class="dash-card__header">
                        <h3><i class="ri-history-line"></i> Aktivitas Terbaru</h3>
                    </div>
                    @if($recentBookings->isEmpty())
                    <div class="dash-empty-state">
                        <div class="dash-empty-state__icon">
                            <i class="ri-suitcase-3-line"></i>
                        </div>
                        <h4>Belum ada perjalanan</h4>
                        <p>Riwayat perjalanan Anda akan muncul di sini setelah Anda melakukan pemesanan pertama.</p>
                        <a href="{{ route('destinations.index') }}" class="button button--gold">
                            Jelajahi Destinasi
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    @else
                    <div class="dash-activity-list">
                        @foreach($recentBookings as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="dash-activity-item">
                            <div class="dash-activity-item__icon">
                                <i class="ri-ticket-2-line"></i>
                            </div>
                            <div class="dash-activity-item__info">
                                <strong>{{ $booking->booking_code }}</strong>
                                <span>{{ $booking->busRoute?->route_name ?? $booking->destination?->name ?? '-' }}</span>
                            </div>
                            <div class="dash-activity-item__meta">
                                <span class="badge badge--{{ $booking->status === 'completed' ? 'green' : ($booking->status === 'cancelled' ? 'red' : 'gold') }}">{{ ucfirst($booking->status) }}</span>
                                <small>{{ $booking->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div style="padding: 1rem 1.5rem; text-align: center;">
                        <a href="{{ route('bookings.index') }}" class="button button--gold" style="display:inline-flex;">
                            Lihat Semua Pesanan <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    @endif
                </section>
            </main>
        </div>
@endsection