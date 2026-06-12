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
                        <input type="text" name="bus_name" class="form-control @error('bus_name') is-invalid @enderror" value="{{ old('bus_name', $bus->bus_name ?? '') }}" required>
                        @error('bus_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Bus</label>
                        <input type="text" name="bus_number" class="form-control @error('bus_number') is-invalid @enderror" value="{{ old('bus_number', $bus->bus_number ?? '') }}" required>
                        @error('bus_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipe</label>
                        <select name="bus_type" class="form-select @error('bus_type') is-invalid @enderror" required>
                            @foreach(['standard', 'executive', 'suite'] as $type)
                                <option value="{{ $type }}" {{ old('bus_type', $bus->bus_type ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        @error('bus_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" min="1" value="{{ old('capacity', $bus->capacity ?? '') }}" required>
                        @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fasilitas</label>
                    <input type="text" name="facilities" class="form-control @error('facilities') is-invalid @enderror" value="{{ old('facilities', $bus->facilities ?? '') }}" placeholder="AC, WiFi, USB Charger, Toilet">
                    <div class="form-text">Pisahkan dengan koma</div>
                    @error('facilities') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['active', 'maintenance', 'retired'] as $s)
                            <option value="{{ $s }}" {{ old('status', $bus->status ?? 'active') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($bus) ? 'Simpan Perubahan' : 'Tambah Bus' }}</button>
                    <a href="{{ route('admin.buses.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
