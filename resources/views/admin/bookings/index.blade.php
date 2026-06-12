@extends('admin.layout')

@section('title', 'Pesanan')
@section('heading', 'Kelola Pesanan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $bookings->total() }} pesanan ditemukan</span>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['pending', 'confirmed', 'completed', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pengguna</th>
                        <th>Rute / Destinasi</th>
                        <th>Tanggal</th>
                        <th>Penumpang</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td><code>{{ $booking->booking_code }}</code></td>
                            <td>{{ $booking->user?->name ?? '-' }}</td>
                            <td>{{ $booking->busRoute?->route_name ?? $booking->destination?->name ?? '-' }}</td>
                            <td>{{ $booking->travel_date->format('d M Y') }}</td>
                            <td>{{ $booking->num_passengers }}</td>
                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="width:auto;font-size:.8rem;" onchange="this.form.submit()">
                                        @foreach(['pending', 'confirmed', 'completed', 'cancelled'] as $s)
                                            <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                @php $pb = match($booking->payment_status) { 'paid' => 'success', 'refunded' => 'gold', default => 'neutral' }; @endphp
                                <span class="admin-badge {{ $pb }}">{{ $booking->payment_status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $bookings->withQueryString()->links() }}</div>
@endsection
