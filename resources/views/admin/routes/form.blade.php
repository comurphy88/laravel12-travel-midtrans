@extends('admin.layout')

@section('title', isset($route) ? 'Edit Rute' : 'Tambah Rute')
@section('heading', isset($route) ? 'Edit Rute' : 'Tambah Rute')

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
            <form method="POST" action="{{ isset($route) ? route('admin.routes.update', $route) : route('admin.routes.store') }}">
                @csrf
                @if(isset($route)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kota Asal <span class="text-danger">*</span></label>
                        <input type="text" name="origin" class="form-control"
                            value="{{ old('origin', $route->origin ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kota Tujuan <span class="text-danger">*</span></label>
                        <input type="text" name="destination" class="form-control"
                            value="{{ old('destination', $route->destination ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jarak (km) <span class="text-danger">*</span></label>
                        <input type="number" name="distance_km" class="form-control" min="1"
                            value="{{ old('distance_km', $route->distance_km ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
                                {{ old('active', $route->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($route) ? 'Simpan Perubahan' : 'Tambah Rute' }}
                    </button>
                    <a href="{{ route('admin.routes.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
