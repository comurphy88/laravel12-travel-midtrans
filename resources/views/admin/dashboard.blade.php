@extends('admin.layout')

@section('title', 'Dasbor')
@section('heading', 'Dashboard Analitik')

@section('styles')
<style>
    .topbar-badge {
        background: rgba(212,175,55,0.12);
        color: #9f7c39;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .stat-card-v2 {
        background: #fff;
        border: 1px solid #e7ddd0;
        border-radius: 10px;
        padding: 14px 16px;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stat-card-v2 .accent-bar {
        position: absolute;
        top: 0; left: 0;
        width: 3px;
        height: 100%;
        background: #d4af37;
    }
    .stat-card-v2 .accent-bar.green  { background: #2da44e; }
    .stat-card-v2 .accent-bar.blue   { background: #0969da; }
    .stat-card-v2 .accent-bar.purple { background: #7c3aed; }

    .stat-card-v2 .stat-label {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-card-v2 .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1;
        font-family: 'Cinzel', serif;
    }

    .stat-card-v2 .stat-sub {
        font-size: 10px;
        color: #aaa;
        margin-top: 4px;
    }

    .status-card-v2 {
        background: #fff;
        border: 1px solid #e7ddd0;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        height: 100%;
    }

    .status-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 18px;
    }

    .status-icon-box.warn   { background: rgba(212,175,55,0.12); color: #9f7c39; }
    .status-icon-box.ok     { background: rgba(46,164,78,0.12);  color: #2da44e; }
    .status-icon-box.done   { background: rgba(9,105,218,0.12);  color: #0969da; }
    .status-icon-box.off    { background: rgba(207,34,46,0.12);  color: #cf222e; }

    .status-num {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a2e;
        font-family: 'Cinzel', serif;
    }

    .status-lbl {
        font-size: 11px;
        color: #888;
        margin-top: 2px;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 16px;
        align-items: start;
    }

    .side-panel {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .panel-card {
        background: #fff;
        border: 1px solid #e7ddd0;
        border-radius: 10px;
        overflow: hidden;
    }

    .panel-card-head {
        padding: 11px 16px;
        border-bottom: 1px solid #e7ddd0;
        font-size: 12px;
        font-weight: 600;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 6px;
        font-family: 'Cinzel', serif;
    }

    .panel-card-head i { color: #9f7c39; }

    .quick-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
    }

    .qs-item {
        padding: 14px 10px;
        text-align: center;
        border-right: 1px solid #e7ddd0;
    }

    .qs-item:last-child { border-right: none; }

    .qs-num {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
        font-family: 'Cinzel', serif;
    }

    .qs-lbl {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 3px;
    }

    .act-list { padding: 0; }

    .act-item {
        padding: 10px 16px;
        border-bottom: 1px solid #f0ece5;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .act-item:last-child { border-bottom: none; }

    .act-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-top: 5px;
        flex-shrink: 0;
    }

    .act-dot.red    { background: #cf222e; }
    .act-dot.amber  { background: #d4af37; }
    .act-dot.green  { background: #2da44e; }

    .act-text { font-size: 12px; color: #1a1a2e; line-height: 1.4; }
    .act-time { font-size: 11px; color: #aaa; margin-top: 2px; }

    .table-card-v2 {
        background: #fff;
        border: 1px solid #e7ddd0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-card-head {
        padding: 12px 16px;
        border-bottom: 1px solid #e7ddd0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-card-head span {
        font-size: 12px;
        font-weight: 600;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 6px;
        font-family: 'Cinzel', serif;
    }

    .table-card-head i { color: #9f7c39; }
    .table-card-v2 table { margin-bottom: 0; }

    .table-card-v2 thead th {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9f7c39;
        padding: 8px 16px;
        background: #faf8f3;
        border-bottom: 1px solid #e7ddd0;
        font-family: 'Cinzel', serif;
        font-weight: 600;
    }

    .table-card-v2 tbody td {
        font-size: 12px;
        padding: 9px 16px;
        border-bottom: 1px solid #f0ece5;
        vertical-align: middle;
        color: #1a1a2e;
    }

    .table-card-v2 tbody tr:last-child td { border-bottom: none; }
    .table-card-v2 tbody tr:hover td { background: #faf8f3; }

    .code-pill {
        font-family: monospace;
        font-size: 11px;
        color: #0969da;
        background: #e6f1fb;
        padding: 2px 6px;
        border-radius: 4px;
    }

    @media (max-width: 992px) {
        .bottom-grid { grid-template-columns: 1fr; }
        .side-panel { display: grid; grid-template-columns: 1fr 1fr; }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
    <span class="topbar-badge"><i class="bi bi-shield-check"></i> Admin</span>
</div>

{{-- Row 1: Main Stats --}}
<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card-v2">
            <div class="accent-bar"></div>
            <div class="stat-label"><i class="bi bi-ticket-perforated"></i> Total Pesanan</div>
            <div class="stat-value">{{ $stats['bookings'] }}</div>
            <div class="stat-sub">Semua status</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card-v2">
            <div class="accent-bar green"></div>
            <div class="stat-label"><i class="bi bi-cash-stack"></i> Pendapatan</div>
            <div class="stat-value" style="font-size:16px">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="stat-sub">confirmed / completed</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card-v2">
            <div class="accent-bar blue"></div>
            <div class="stat-label"><i class="bi bi-people"></i> Pengguna</div>
            <div class="stat-value">{{ $stats['users'] }}</div>
            <div class="stat-sub">Terdaftar</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card-v2">
            <div class="accent-bar purple"></div>
            <div class="stat-label"><i class="bi bi-signpost-2"></i> Rute Aktif</div>
            <div class="stat-value">
                {{ $stats['routes_active'] }}<span style="font-size:13px;color:#aaa">/{{ $stats['routes_all'] }}</span>
            </div>
            <div class="stat-sub">{{ $stats['schedules_active'] }} jadwal aktif</div>
        </div>
    </div>
</div>

{{-- Row 2: Status Breakdown --}}
<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="status-card-v2">
            <div class="status-icon-box warn"><i class="bi bi-hourglass-split"></i></div>
            <div class="status-num">{{ $stats['bookings_pending'] }}</div>
            <div class="status-lbl">Tertunda</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="status-card-v2">
            <div class="status-icon-box ok"><i class="bi bi-check-circle"></i></div>
            <div class="status-num">{{ $stats['bookings_confirmed'] }}</div>
            <div class="status-lbl">Terkonfirmasi</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="status-card-v2">
            <div class="status-icon-box done"><i class="bi bi-check-all"></i></div>
            <div class="status-num">{{ $stats['bookings_completed'] }}</div>
            <div class="status-lbl">Selesai</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="status-card-v2">
            <div class="status-icon-box off"><i class="bi bi-x-circle"></i></div>
            <div class="status-num">{{ $stats['bookings_cancelled'] }}</div>
            <div class="status-lbl">Dibatalkan</div>
        </div>
    </div>
</div>

{{-- Row 3: Table + Side Panel --}}
<div class="bottom-grid">

    {{-- Tabel Pesanan Terbaru --}}
    <div class="table-card-v2">
        <div class="table-card-head">
            <span><i class="bi bi-clock-history"></i> Pesanan Terbaru</span>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px">Lihat Semua</a>
        </div>
        @if($recentBookings->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2"></i>
                <p class="mt-2 small">Belum ada pesanan.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pengguna</th>
                        <th>Rute</th>
                        <th>Bus</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $booking)
                    @php $bus = $booking->schedule?->bus; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-decoration-none">
                                <span class="code-pill">{{ Str::limit($booking->booking_code, 12) }}</span>
                            </a>
                        </td>
                        <td>{{ $booking->user?->name ?? '-' }}</td>
                        <td style="white-space:nowrap; font-size:11px;">
                            {{ $booking->origin ?? '-' }} → {{ $booking->destination ?? '-' }}
                        </td>
                        <td style="font-size:11px;">{{ $bus?->bus_name ?? '-' }}</td>
                        <td>{{ $booking->travel_date->format('d M Y') }}</td>
                        <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badgeClass = match($booking->status) {
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'confirmed' => 'gold',
                                    default     => 'warning'
                                };
                            @endphp
                            <span class="admin-badge {{ $badgeClass }}">{{ $booking->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Side Panel --}}
    <div class="side-panel">

        {{-- Ringkasan Konten --}}
        <div class="panel-card">
            <div class="panel-card-head">
                <i class="bi bi-grid"></i> Ringkasan Konten
            </div>
            <div class="quick-stats-grid">
                <a href="{{ route('admin.buses.index') }}" class="qs-item text-decoration-none">
                    <div class="qs-num">{{ $stats['buses_active'] }}</div>
                    <div class="qs-lbl">Bus</div>
                </a>
                <a href="{{ route('admin.routes.index') }}" class="qs-item text-decoration-none">
                    <div class="qs-num">{{ $stats['routes_active'] }}</div>
                    <div class="qs-lbl">Rute</div>
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="qs-item text-decoration-none">
                    <div class="qs-num">{{ $stats['schedules_active'] }}</div>
                    <div class="qs-lbl">Jadwal</div>
                </a>
            </div>
            <div class="quick-stats-grid" style="border-top:1px solid #e7ddd0;">
                <a href="{{ route('admin.galleries.index') }}" class="qs-item text-decoration-none">
                    <div class="qs-num">{{ $stats['galleries'] }}</div>
                    <div class="qs-lbl">Galeri</div>
                </a>
                <a href="{{ route('admin.promo-codes.index') }}" class="qs-item text-decoration-none">
                    <div class="qs-num">{{ $stats['promo_codes'] }}</div>
                    <div class="qs-lbl">Promo</div>
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="qs-item text-decoration-none">
                    <div class="qs-num">{{ $stats['reviews_pending'] }}</div>
                    <div class="qs-lbl">Review</div>
                </a>
            </div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="panel-card">
            <div class="panel-card-head">
                <i class="bi bi-activity"></i> Aktivitas Terbaru
            </div>
            <div class="act-list">
                @forelse($recentBookings->take(4) as $booking)
                    @php
                        $dot = match($booking->status) {
                            'cancelled'           => 'red',
                            'confirmed','completed' => 'green',
                            default               => 'amber'
                        };
                        $action = match($booking->status) {
                            'cancelled' => 'dibatalkan',
                            'confirmed' => 'dikonfirmasi',
                            'completed' => 'selesai',
                            default     => 'menunggu konfirmasi'
                        };
                    @endphp
                    <div class="act-item">
                        <div class="act-dot {{ $dot }}"></div>
                        <div>
                            <div class="act-text">
                                Pesanan {{ Str::limit($booking->booking_code, 12) }} {{ $action }}
                            </div>
                            <div class="act-time">
                                {{ $booking->origin ?? '-' }} → {{ $booking->destination ?? '-' }}
                                · {{ $booking->user?->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">Tidak ada aktivitas.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
