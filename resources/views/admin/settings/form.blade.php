@extends('admin.layout')

@section('title', isset($setting) ? 'Edit Pengaturan' : 'Tambah Pengaturan')
@section('heading', isset($setting) ? 'Edit Pengaturan' : 'Tambah Pengaturan')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($setting) ? route('admin.settings.update', $setting) : route('admin.settings.store') }}">
                @csrf
                @if(isset($setting)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Kunci Pengaturan</label>
                    <input type="text" name="setting_key" class="form-control @error('setting_key') is-invalid @enderror" value="{{ old('setting_key', $setting->setting_key ?? '') }}" required placeholder="site_name">
                    @error('setting_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nilai</label>
                    <textarea name="setting_value" class="form-control @error('setting_value') is-invalid @enderror" rows="3">{{ old('setting_value', $setting->setting_value ?? '') }}</textarea>
                    @error('setting_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="setting_type" class="form-select @error('setting_type') is-invalid @enderror" required>
                        @foreach(['string', 'number', 'boolean', 'json'] as $type)
                            <option value="{{ $type }}" {{ old('setting_type', $setting->setting_type ?? 'string') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('setting_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $setting->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($setting) ? 'Simpan Perubahan' : 'Tambah Pengaturan' }}</button>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
