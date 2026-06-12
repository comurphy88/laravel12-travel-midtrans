@extends('admin.layout')

@section('title', isset($gallery) ? 'Edit Foto' : 'Tambah Foto')
@section('heading', isset($gallery) ? 'Edit Foto' : 'Tambah Foto')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($gallery) ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}">
                @csrf
                @if(isset($gallery)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $gallery->title ?? '') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Gambar</label>
                    <input type="url" name="image" class="form-control @error('image') is-invalid @enderror" value="{{ old('image', $gallery->image ?? '') }}" required placeholder="https://example.com/image.jpg">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($gallery) && $gallery->image)
                        <div class="mt-2"><img src="{{ $gallery->image }}" alt="Preview" style="max-height:120px;border-radius:8px;border:1px solid var(--admin-border);"></div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($gallery) ? 'Simpan Perubahan' : 'Tambah Foto' }}</button>
                    <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
