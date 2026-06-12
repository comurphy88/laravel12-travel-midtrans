@extends('admin.layout')

@section('title', isset($busRoute) ? 'Edit Rute' : 'Tambah Rute')
@section('heading', isset($busRoute) ? 'Edit Rute' : 'Tambah Rute')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($busRoute) ? route('admin.bus-routes.update', $busRoute) : route('admin.bus-routes.store') }}">
                @csrf
                @if(isset($busRoute)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Nama Rute</label>
                    <input type="text" name="route_name" class="form-control @error('route_name') is-invalid @enderror" value="{{ old('route_name', $busRoute->route_name ?? '') }}" required>
                    @error('route_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Asal</label>
                        <input type="text" name="origin" class="form-control @error('origin') is-invalid @enderror" value="{{ old('origin', $busRoute->origin ?? '') }}" required>
                        @error('origin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tujuan</label>
                        <input type="text" name="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination', $busRoute->destination ?? '') }}" required>
                        @error('destination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jarak</label>
                        <input type="text" name="distance" class="form-control @error('distance') is-invalid @enderror" value="{{ old('distance', $busRoute->distance ?? '') }}" placeholder="150 km">
                        @error('distance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Durasi</label>
                        <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration', $busRoute->duration ?? '') }}" placeholder="3 jam">
                        @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jam Berangkat</label>
                        <input type="time" name="departure_time" class="form-control @error('departure_time') is-invalid @enderror" value="{{ old('departure_time', isset($busRoute) ? substr($busRoute->departure_time, 0, 5) : '') }}">
                        @error('departure_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam Tiba</label>
                        <input type="time" name="arrival_time" class="form-control @error('arrival_time') is-invalid @enderror" value="{{ old('arrival_time', isset($busRoute) ? substr($busRoute->arrival_time, 0, 5) : '') }}">
                        @error('arrival_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Harga (IDR)</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" step="1000" min="0" value="{{ old('price', isset($busRoute) ? (int)$busRoute->price : '') }}" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bus</label>
                        <select name="bus_id" class="form-select @error('bus_id') is-invalid @enderror" required>
                            <option value="">— Pilih Bus —</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}" {{ old('bus_id', $busRoute->bus_id ?? '') == $bus->id ? 'selected' : '' }}>
                                    {{ $bus->bus_name }} ({{ $bus->bus_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('bus_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="hidden" name="active" value="0">
                        <input class="form-check-input" type="checkbox" name="active" id="fActive" value="1" {{ old('active', $busRoute->active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="fActive">Aktif</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ isset($busRoute) ? 'Simpan Perubahan' : 'Tambah Rute' }}</button>
                    <a href="{{ route('admin.bus-routes.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
