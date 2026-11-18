@extends('admin.layout')

@section('title', 'Jadwal')
@section('heading', 'Kelola Jadwal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="route_id" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Rute</option>
                @foreach($routes as $r)
                    <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->origin }} → {{ $r->destination }}
                    </option>
                @endforeach
            </select>
            <select name="bus_id" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Bus</option>
                @foreach($buses as $b)
                    <option value="{{ $b->id }}" {{ request('bus_id') == $b->id ? 'selected' : '' }}>
                        {{ $b->bus_name }}
                    </option>
                @endforeach
            </select>
            <select name="active" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </form>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rute</th>
                        <th>Bus</th>
                        <th>Jam Berangkat</th>
                        <th>Kapasitas</th>
                        <th>Booking</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td style="white-space:nowrap;">
                                <strong>{{ $schedule->route?->origin ?? '-' }}</strong>
                                → {{ $schedule->route?->destination ?? '-' }}
                            </td>
                            <td>{{ $schedule->bus?->bus_name ?? '-' }}</td>
                            <td>
                            </td>
                            <td><strong>{{ substr($schedule->departure_time, 0, 5) }}</strong></td>
                            <td>{{ $schedule->bus?->capacity ?? '-' }} kursi</td>
                            <td>{{ $schedule->bookings_count }} booking</td>
                            <td>
                                @if($schedule->active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.schedules.toggle', $schedule) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $schedule->active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            {{ $schedule->active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @if($schedule->bookings_count === 0)
                                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                                              onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $schedules->withQueryString()->links() }}</div>
@endsection
