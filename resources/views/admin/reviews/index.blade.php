@extends('admin.layout')

@section('title', 'Review')
@section('heading', 'Kelola Review')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $reviews->total() }} review ditemukan</span>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['pending', 'approved', 'rejected'] as $s)
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
                        <th>#</th>
                        <th>Pengguna</th>
                        <th>Destinasi</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->user?->name ?? '-' }}</td>
                            <td>{{ $review->destination?->name ?? '-' }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}" style="font-size:.8rem;"></i>
                                @endfor
                            </td>
                            <td>{{ Str::limit($review->comment, 50) }}</td>
                            <td>
                                <form action="{{ route('admin.reviews.status', $review) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="width:auto;font-size:.8rem;" onchange="this.form.submit()">
                                        @foreach(['pending', 'approved', 'rejected'] as $s)
                                            <option value="{{ $s }}" {{ $review->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>{{ $review->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus review ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada review.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $reviews->withQueryString()->links() }}</div>
@endsection
