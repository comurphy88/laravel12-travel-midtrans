@extends('admin.layout')

@section('title', 'Detail Review')
@section('heading', 'Detail Review #' . $review->id)

@section('content')
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Info Review</div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Pengguna</div>
                        <div class="col-sm-8">{{ $review->user?->name ?? '-' }} ({{ $review->user?->email ?? '-' }})</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Destinasi</div>
                        <div class="col-sm-8">{{ $review->destination?->name ?? '-' }}</div>
                    </div>
                    @if($review->booking)
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Pesanan</div>
                        <div class="col-sm-8"><code>{{ $review->booking->booking_code }}</code></div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Rating</div>
                        <div class="col-sm-8">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                            @endfor
                            ({{ $review->rating }}/5)
                        </div>
                    </div>
                    @if($review->title)
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Judul</div>
                        <div class="col-sm-8">{{ $review->title }}</div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Komentar</div>
                        <div class="col-sm-8">{{ $review->comment }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Tanggal</div>
                        <div class="col-sm-8">{{ $review->created_at->format('d M Y H:i') }}</div>
                    </div>
                    @if($review->reviewer)
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Direview Oleh</div>
                        <div class="col-sm-8">{{ $review->reviewer->name }} pada {{ $review->reviewed_at?->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Ubah Status</div>
                <div class="card-body">
                    <form action="{{ route('admin.reviews.status', $review) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                @foreach(['pending', 'approved', 'rejected'] as $s)
                                    <option value="{{ $s }}" {{ $review->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
