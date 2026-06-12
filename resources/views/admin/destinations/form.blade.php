@extends('admin.layout')

@section('title', isset($destination) ? 'Edit Destinasi' : 'Tambah Destinasi')
@section('heading', isset($destination) ? 'Edit Destinasi' : 'Tambah Destinasi')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($destination) ? route('admin.destinations.update', $destination) : route('admin.destinations.store') }}">
                @csrf
                @if(isset($destination)) @method('PUT') @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Destinasi</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $destination->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $destination->location ?? '') }}" required>
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $destination->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Gambar</label>
                    <input type="url" name="image" class="form-control @error('image') is-invalid @enderror" value="{{ old('image', $destination->image ?? '') }}" required placeholder="https://example.com/image.jpg">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($destination) && $destination->image)
                        <div class="mt-2"><img src="{{ $destination->image }}" alt="Preview" style="max-height:120px;border-radius:8px;border:1px solid var(--admin-border);"></div>
                    @endif
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Harga (IDR)</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" step="1000" min="0" value="{{ old('price', isset($destination) ? (int)$destination->price : '') }}" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rating (0-5)</label>
                        <input type="number" name="rating" class="form-control @error('rating') is-invalid @enderror" step="0.1" min="0" max="5" value="{{ old('rating', $destination->rating ?? '') }}" required>
                        @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="hidden" name="active" value="0">
                        <input class="form-check-input" type="checkbox" name="active" id="fActive" value="1" {{ old('active', $destination->active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="fActive">Aktif</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($destination) ? 'Simpan Perubahan' : 'Tambah Destinasi' }}</button>
                    <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
