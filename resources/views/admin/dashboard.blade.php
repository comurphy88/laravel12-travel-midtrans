@extends('admin.layout')

@section('title', 'Dasbor')
@section('heading', 'Dashboard Analitik')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</span>
    </div>

    <!-- Row 1: Main Stats (4 cols) -->
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-ticket-perforated"></i> Total Pesanan</h6>
                <h3 class="mb-0">{{ $stats['bookings'] }}</h3>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-cash-stack"></i> Pendapatan</h6>
                <h3 class="mb-0" style="font-size:1.1rem;">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
                <small class="text-muted">confirmed/completed</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-people"></i> Pengguna</h6>
                <h3 class="mb-0">{{ $stats['users'] }}</h3>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-geo-alt"></i> Destinasi Aktif</h6>
                <h3 class="mb-0">{{ $stats['destinations_active'] }}<span style="font-size:0.75rem;">/ {{ $stats['destinations_all'] }}</span></h3>
            </div>
        </div>
    </div>

    <!-- Row 2: Status Breakdown (4 cols) -->
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-hourglass-split"></i> Tertunda</h6>
                <h3 class="mb-0">{{ $stats['bookings_pending'] }}</h3>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-check-circle"></i> Terkonfirmasi</h6>
                <h3 class="mb-0">{{ $stats['bookings_confirmed'] }}</h3>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-check-all"></i> Selesai</h6>
                <h3 class="mb-0">{{ $stats['bookings_completed'] }}</h3>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-x-circle"></i> Dibatalkan</h6>
                <h3 class="mb-0">{{ $stats['bookings_cancelled'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Row 3: Quick Stats (3 cols) -->
    <div class="row g-2 mb-3">
        <div class="col-lg-4 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-images"></i> Galeri</h6>
                <h3 class="mb-0">{{ $stats['galleries'] }}</h3>
                <a href="{{ route('admin.galleries.index') }}" class="stretched-link text-decoration-none"></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-tag"></i> Kode Promo</h6>
                <h3 class="mb-0">{{ $stats['promo_codes'] }}</h3>
                <a href="{{ route('admin.promo-codes.index') }}" class="stretched-link text-decoration-none"></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card stat-card p-3 h-100">
                <h6 class="text-muted mb-1"><i class="bi bi-star"></i> Review</h6>
                <h3 class="mb-0">{{ $stats['reviews'] }}</h3>
                <a href="{{ route('admin.reviews.index') }}" class="stretched-link text-decoration-none"></a>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history"></i> Pesanan Terbaru</span>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
        </div>

        @if($recentBookings->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">Belum ada booking.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-sm">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pengguna</th>
                        <th>Destinasi</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                        <tr>
                            <td><code>{{ $booking->booking_code }}</code></td>
                            <td>{{ $booking->user?->name ?? '-' }}</td>
                            <td>{{ $booking->destination?->name ?? '-' }}</td>
                            <td>{{ $booking->travel_date->format('d M Y') }}</td>
                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td>
                                @php $sb = match($booking->status) { 'completed' => 'success', 'cancelled' => 'danger', 'confirmed' => 'gold', default => 'warning' }; @endphp
                                <span class="admin-badge {{ $sb }}">{{ $booking->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
@endsection
