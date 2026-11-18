@extends('admin.layout')

@section('title', isset($schedule) ? 'Edit Jadwal' : 'Tambah Jadwal')
@section('heading', isset($schedule) ? 'Edit Jadwal' : 'Tambah Jadwal')

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($schedule) ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}">
                @csrf
                @if(isset($schedule)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Rute <span class="text-danger">*</span></label>
                        <select name="route_id" class="form-select" required>
                            <option value="">-- Pilih Rute --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}"
                                    {{ old('route_id', $schedule->route_id ?? '') == $route->id ? 'selected' : '' }}>
                                    {{ $route->origin }} → {{ $route->destination }} ({{ $route->distance_km }} km)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bus <span class="text-danger">*</span></label>
                        <select name="bus_id" class="form-select" required>
                            <option value="">-- Pilih Bus --</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}"
                                    {{ old('bus_id', $schedule->bus_id ?? '') == $bus->id ? 'selected' : '' }}>
                                    {{ $bus->bus_name }} · {{ $bus->capacity }} kursi · Rp {{ number_format($bus->price_per_km, 0, ',', '.') }}/km
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam Berangkat <span class="text-danger">*</span></label>
                        <input type="time" name="departure_time" class="form-control"
                            value="{{ old('departure_time', isset($schedule) ? substr($schedule->departure_time, 0, 5) : '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
                                {{ old('active', $schedule->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($schedule) ? 'Simpan Perubahan' : 'Tambah Jadwal' }}
                    </button>
                    <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
