@extends('admin.layout')

@section('title', 'Kelola Bus')
@section('heading', 'Kelola Armada Bus')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $buses->total() }} bus terdaftar</span>
        <a href="{{ route('admin.buses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Bus</a>
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
                        <th>Nama</th>
                        <th>Nomor</th>
                        <th>Kapasitas</th>
                        <th>Harga/km</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                        <tr>
                            <td><strong>{{ $bus->bus_name }}</strong></td>
                            <td><code>{{ $bus->bus_number }}</code></td>
                            <td>{{ $bus->capacity }} kursi</td>
                            <td>Rp {{ number_format($bus->price_per_km, 0, ',', '.') }}/km</td>
                            <td>{{ $bus->schedules_count }} jadwal</td>
                            <td>
                                @php $sb = match($bus->status) { 'active' => 'success', 'maintenance' => 'warning', default => 'neutral' }; @endphp
                                <span class="admin-badge {{ $sb }}">{{ ucfirst($bus->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.buses.edit', $bus) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus bus {{ $bus->bus_name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada bus.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $buses->withQueryString()->links() }}</div>
@endsection
