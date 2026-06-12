@extends('admin.layout')

@section('title', isset($testimonial) ? 'Edit Testimoni' : 'Tambah Testimoni')
@section('heading', isset($testimonial) ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}">
                @csrf
                @if(isset($testimonial)) @method('PUT') @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $testimonial->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Peran</label>
                        <input type="text" name="role" class="form-control @error('role') is-invalid @enderror" value="{{ old('role', $testimonial->role ?? '') }}" required placeholder="Pelancong, Pengusaha, dll.">
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                    @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Teks Testimoni</label>
                    <textarea name="text" class="form-control @error('text') is-invalid @enderror" rows="4" required>{{ old('text', $testimonial->text ?? '') }}</textarea>
                    @error('text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Foto</label>
                    <input type="url" name="image" class="form-control @error('image') is-invalid @enderror" value="{{ old('image', $testimonial->image ?? '') }}" required placeholder="https://example.com/photo.jpg">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($testimonial) && $testimonial->image)
                        <div class="mt-2"><img src="{{ $testimonial->image }}" alt="Preview" style="max-height:80px;border-radius:50%;"></div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($testimonial) ? 'Simpan Perubahan' : 'Tambah Testimoni' }}</button>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
