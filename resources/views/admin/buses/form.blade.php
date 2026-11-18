@extends('admin.layout')

@section('title', isset($bus) ? 'Edit Bus' : 'Tambah Bus')
@section('heading', isset($bus) ? 'Edit Bus' : 'Tambah Bus')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($bus) ? route('admin.buses.update', $bus) : route('admin.buses.store') }}">
                @csrf
                @if(isset($bus)) @method('PUT') @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Bus</label>
                        <input type="text" name="bus_name" class="form-control @error('bus_name') is-invalid @enderror"
                            value="{{ old('bus_name', $bus->bus_name ?? '') }}" required>
                        @error('bus_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Bus</label>
                        <input type="text" name="bus_number" class="form-control @error('bus_number') is-invalid @enderror"
                            value="{{ old('bus_number', $bus->bus_number ?? '') }}" required>
                        @error('bus_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                            min="1" value="{{ old('capacity', $bus->capacity ?? '') }}" required>
                        @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga per km (Rp)</label>
                        <input type="number" name="price_per_km" class="form-control @error('price_per_km') is-invalid @enderror"
                            min="0" step="0.01" value="{{ old('price_per_km', $bus->price_per_km ?? '') }}" required>
                        <div class="form-text">Contoh: 900 = Rp 900/km per penumpang</div>
                        @error('price_per_km') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['active', 'maintenance', 'retired'] as $s)
                            <option value="{{ $s }}" {{ old('status', $bus->status ?? 'active') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> {{ isset($bus) ? 'Simpan Perubahan' : 'Tambah Bus' }}
                    </button>
                    <a href="{{ route('admin.buses.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
