@extends('admin.layout')

@section('title', isset($policy) ? 'Edit Kebijakan Pembatalan' : 'Tambah Kebijakan Pembatalan')
@section('heading', isset($policy) ? 'Edit Kebijakan Pembatalan' : 'Tambah Kebijakan Pembatalan')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($policy) ? route('admin.cancellation-policies.update', $policy) : route('admin.cancellation-policies.store') }}">
                @csrf
                @if(isset($policy)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Nama Kebijakan</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $policy->name ?? '') }}" required placeholder="Pembatalan H-24">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jam Sebelum Keberangkatan</label>
                        <input type="number" name="hours_before_travel" class="form-control @error('hours_before_travel') is-invalid @enderror" min="0" value="{{ old('hours_before_travel', $policy->hours_before_travel ?? '') }}" required>
                        @error('hours_before_travel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Persentase Refund (%)</label>
                        <input type="number" name="refund_percentage" class="form-control @error('refund_percentage') is-invalid @enderror" step="0.01" min="0" max="100" value="{{ old('refund_percentage', $policy->refund_percentage ?? '') }}" required>
                        @error('refund_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $policy->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1" class="form-check-input" id="activeCheck" {{ old('active', $policy->active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeCheck">Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($policy) ? 'Simpan Perubahan' : 'Tambah Kebijakan' }}</button>
                    <a href="{{ route('admin.cancellation-policies.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
