@extends('admin.layout')

@section('title', 'Log Email')
@section('heading', 'Log Email')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $emailLogs->total() }} log ditemukan</span>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pengguna</th>
                        <th>Penerima</th>
                        <th>Subjek</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Dikirim</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emailLogs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->user?->name ?? '-' }}</td>
                            <td>{{ $log->recipient_email }}</td>
                            <td>{{ Str::limit($log->subject, 40) }}</td>
                            <td><span class="admin-badge neutral">{{ $log->type }}</span></td>
                            <td>
                                @php $eb = $log->status === 'sent' ? 'success' : 'danger'; @endphp
                                <span class="admin-badge {{ $eb }}">{{ $log->status === 'sent' ? 'Terkirim' : 'Gagal' }}</span>
                                @if($log->error_message)
                                    <small class="d-block text-danger">{{ Str::limit($log->error_message, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $log->sent_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada log email.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $emailLogs->links() }}</div>
@endsection
