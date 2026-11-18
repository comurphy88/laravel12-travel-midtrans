@extends('admin.layout')

@section('title', 'Rute')
@section('heading', 'Kelola Rute')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" style="width:200px;"
                placeholder="Cari kota asal/tujuan..." value="{{ request('search') }}">
            <select name="active" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </form>
        <a href="{{ route('admin.routes.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah Rute
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
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Jarak (km)</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                        <tr>
                            <td><strong>{{ $route->origin }}</strong></td>
                            <td><strong>{{ $route->destination }}</strong></td>
                            <td>{{ number_format($route->distance_km) }} km</td>
                            <td>{{ $route->schedules_count }} jadwal</td>
                            <td>
                                @if($route->active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.routes.toggle', $route) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $route->active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            {{ $route->active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @if($route->schedules_count === 0)
                                        <form method="POST" action="{{ route('admin.routes.destroy', $route) }}"
                                              onsubmit="return confirm('Hapus rute ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada rute.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $routes->withQueryString()->links() }}</div>
@endsection
