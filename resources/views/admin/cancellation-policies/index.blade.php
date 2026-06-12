@extends('admin.layout')

@section('title', 'Kebijakan Pembatalan')
@section('heading', 'Kelola Kebijakan Pembatalan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $policies->total() }} kebijakan ditemukan</span>
        <a href="{{ route('admin.cancellation-policies.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kebijakan</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jam Sebelum Keberangkatan</th>
                        <th>Persentase Refund</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $policy)
                        <tr>
                            <td>{{ $policy->id }}</td>
                            <td>{{ $policy->name }}</td>
                            <td>{{ $policy->hours_before_travel }} jam</td>
                            <td>{{ $policy->refund_percentage }}%</td>
                            <td>
                                <span class="admin-badge {{ $policy->active ? 'success' : 'neutral' }}">{{ $policy->active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.cancellation-policies.edit', $policy) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.cancellation-policies.destroy', $policy) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kebijakan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada kebijakan pembatalan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $policies->links() }}</div>
@endsection
