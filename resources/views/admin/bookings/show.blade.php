@extends('admin.layout')

@section('title', 'Detail Pesanan')
@section('heading', 'Pesanan ' . $booking->booking_code)

@section('content')
    @php
        $bus      = $booking->schedule?->bus;
        $route    = $booking->schedule?->route;
    @endphp

    {{-- Status update --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="fw-semibold">Ubah Status Pesanan</span>
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="d-flex gap-2 align-items-center">
                @csrf @method('PATCH')
                <select name="status" class="form-select form-select-sm" style="width:auto;">
                    @foreach(['pending', 'confirmed', 'completed', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Info Booking --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-info-circle"></i> Info Pesanan</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Kode Booking</small>
                            <strong>{{ $booking->booking_code }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Status</small>
                            @php $sb = match($booking->status) { 'completed' => 'success', 'cancelled' => 'danger', 'confirmed' => 'info', default => 'warning' }; @endphp
                            <span class="badge bg-{{ $sb }}">{{ $booking->status }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Tanggal Perjalanan</small>
                            <strong>{{ $booking->travel_date->format('d M Y') }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Jam Berangkat</small>
                            <strong>{{ $booking->departure_time ? substr($booking->departure_time, 0, 5) . ' WIB' : '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Jumlah Penumpang</small>
                            <strong>{{ $booking->num_passengers }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Total Harga</small>
                            <strong style="color:var(--admin-gold);">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Pembayaran</small>
                            @php $pb = match($booking->payment_status) { 'paid' => 'success', 'refunded' => 'info', default => 'secondary' }; @endphp
                            <span class="badge bg-{{ $pb }}">{{ $booking->payment_status }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Dibuat</small>
                            <strong>{{ $booking->created_at->format('d M Y H:i') }}</strong>
                        </div>
                        @if($booking->discount_amount > 0)
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Diskon</small>
                                <strong class="text-success">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                        @if($booking->notes)
                            <div class="col-12">
                                <small class="text-muted d-block">Catatan</small>
                                <strong>{{ $booking->notes }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-person"></i> Pelanggan</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Nama</small>
                            <strong>{{ $booking->user?->name ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $booking->user?->email ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Telepon</small>
                            <strong>{{ $booking->user?->phone ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Kota</small>
                            <strong>{{ $booking->user?->city ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Perjalanan --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-signpost-2"></i> Detail Perjalanan</div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Asal → Tujuan</small>
                    <strong>{{ $booking->origin ?? '-' }} → {{ $booking->destination ?? '-' }}</strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Jarak</small>
                    <strong>{{ $booking->distance_km ? $booking->distance_km . ' km' : '-' }}</strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Bus</small>
                    <strong>{{ $bus?->bus_name ?? '-' }}</strong>
                </div>
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Harga/km</small>
                    <strong>Rp {{ number_format($booking->price_per_km, 0, ',', '.') }}</strong>
                </div>

            </div>
        </div>
    </div>

    {{-- Kebijakan Pembatalan --}}
    @if($booking->cancellationPolicy)
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-shield-exclamation"></i> Kebijakan Pembatalan</div>
            <div class="card-body">
                <strong>{{ $booking->cancellationPolicy->name }}</strong>
                <p class="mb-0 text-muted">{{ $booking->cancellationPolicy->description }}</p>
                <small>Refund {{ $booking->cancellationPolicy->refund_percentage }}% jika dibatalkan > {{ $booking->cancellationPolicy->hours_before_travel }} jam sebelum keberangkatan.</small>
            </div>
        </div>
    @endif

    {{-- Penumpang --}}
    @if($booking->passengers->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-people"></i> Penumpang ({{ $booking->passengers->count() }})</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Nama</th><th>Email</th><th>Telepon</th><th>No. Identitas</th><th>Kursi</th></tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passengers as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->email ?? '-' }}</td>
                                <td>{{ $p->phone ?? '-' }}</td>
                                <td>{{ $p->id_number ?? '-' }}</td>
                                <td><strong>{{ $p->seat_number ?? '-' }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Riwayat Pembayaran --}}
    @if($booking->payments->isNotEmpty())
        <div class="card">
            <div class="card-header"><i class="bi bi-credit-card"></i> Riwayat Pembayaran</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Tanggal</th><th>Metode</th><th>ID Transaksi</th><th>Jumlah</th><th>Expired</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($booking->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td>{{ strtoupper($payment->payment_method ?? '-') }}</td>
                                <td><code>{{ $payment->transaction_id ?? '-' }}</code></td>
                                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($payment->expired_at)
                                        <span class="{{ $payment->expired_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                            {{ $payment->expired_at->format('d M Y H:i') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php $ps = match($payment->status) { 'success' => 'success', 'failed' => 'danger', 'refunded' => 'info', default => 'warning' }; @endphp
                                    <span class="badge bg-{{ $ps }}">{{ ucfirst($payment->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
