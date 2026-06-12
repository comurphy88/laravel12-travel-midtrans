@extends('admin.layout')

@section('title', 'Kelola Rute')
@section('heading', 'Kelola Rute Bus')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $routes->total() }} rute terdaftar</span>
        <a href="{{ route('admin.bus-routes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Rute</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rute</th>
                        <th>Asal → Tujuan</th>
                        <th>Bus</th>
                        <th>Jadwal</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                        <tr>
                            <td><strong>{{ $route->route_name }}</strong></td>
                            <td>{{ $route->origin }} <i class="bi bi-arrow-right"></i> {{ $route->destination }}</td>
                            <td>{{ $route->bus?->bus_name ?? '-' }}</td>
                            <td>{{ $route->departure_time ?? '-' }} — {{ $route->arrival_time ?? '-' }}</td>
                            <td>Rp {{ number_format($route->price, 0, ',', '.') }}</td>
                            <td><span class="admin-badge {{ $route->active ? 'success' : 'neutral' }}">{{ $route->active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <a href="{{ route('admin.bus-routes.edit', $route) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.bus-routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rute {{ $route->route_name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada rute.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $routes->withQueryString()->links() }}</div>
@endsection
