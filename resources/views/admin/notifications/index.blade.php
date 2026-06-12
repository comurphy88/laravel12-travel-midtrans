@extends('admin.layout')

@section('title', 'Notifikasi')
@section('heading', 'Kelola Notifikasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $notifications->total() }} notifikasi ditemukan</span>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pengguna</th>
                        <th>Tipe</th>
                        <th>Judul</th>
                        <th>Pesan</th>
                        <th>Dibaca</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notif)
                        <tr>
                            <td>{{ $notif->id }}</td>
                            <td>{{ $notif->user?->name ?? '-' }}</td>
                            <td><span class="admin-badge neutral">{{ $notif->type }}</span></td>
                            <td>{{ $notif->title }}</td>
                            <td>{{ Str::limit($notif->message, 50) }}</td>
                            <td>
                                @if($notif->read_at)
                                    <span class="admin-badge success">{{ $notif->read_at->format('d M Y H:i') }}</span>
                                @else
                                    <span class="admin-badge warning">Belum dibaca</span>
                                @endif
                            </td>
                            <td>{{ $notif->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.notifications.destroy', $notif) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada notifikasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
@endsection
