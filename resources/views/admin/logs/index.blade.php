@extends('admin.layout')

@section('title', 'Log Aktivitas')
@section('heading', 'Log Aktivitas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">150 aktivitas terakhir</span>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
                        <th>Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="admin-badge gold">{{ $log->activity_type }}</span></td>
                            <td>{{ Str::limit($log->description, 80) }}</td>
                            <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
