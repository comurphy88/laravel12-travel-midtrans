@extends('admin.layout')

@section('title', isset($promoCode) ? 'Edit Kode Promo' : 'Tambah Kode Promo')
@section('heading', isset($promoCode) ? 'Edit Kode Promo' : 'Tambah Kode Promo')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($promoCode) ? route('admin.promo-codes.update', $promoCode) : route('admin.promo-codes.store') }}">
                @csrf
                @if(isset($promoCode)) @method('PUT') @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Promo</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $promoCode->code ?? '') }}" required placeholder="DISKON20">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe Diskon</label>
                        <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('discount_type', $promoCode->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('discount_type', $promoCode->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                        </select>
                        @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $promoCode->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Nilai Diskon</label>
                        <input type="number" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror" step="0.01" min="0" value="{{ old('discount_value', $promoCode->discount_value ?? 0) }}" required>
                        @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Min. Pembelian (Rp)</label>
                        <input type="number" name="min_purchase" class="form-control @error('min_purchase') is-invalid @enderror" step="0.01" min="0" value="{{ old('min_purchase', $promoCode->min_purchase ?? 0) }}">
                        @error('min_purchase') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Maks. Diskon (Rp)</label>
                        <input type="number" name="max_discount" class="form-control @error('max_discount') is-invalid @enderror" step="0.01" min="0" value="{{ old('max_discount', $promoCode->max_discount ?? 0) }}">
                        <div class="form-text">0 = tanpa batas</div>
                        @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Batas Penggunaan</label>
                        <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" min="0" value="{{ old('usage_limit', $promoCode->usage_limit ?? 0) }}">
                        <div class="form-text">0 = tidak terbatas</div>
                        @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berlaku Dari</label>
                        <input type="date" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" value="{{ old('valid_from', isset($promoCode) && $promoCode->valid_from ? $promoCode->valid_from->format('Y-m-d') : '') }}">
                        @error('valid_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berlaku Sampai</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until', isset($promoCode) && $promoCode->valid_until ? $promoCode->valid_until->format('Y-m-d') : '') }}">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1" class="form-check-input" id="activeCheck" {{ old('active', $promoCode->active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeCheck">Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($promoCode) ? 'Simpan Perubahan' : 'Tambah Promo' }}</button>
                    <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
