@extends('admin.layout')

@section('title', 'Pengaturan')
@section('heading', 'Pengaturan Situs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $settings->count() }} pengaturan</span>
        <a href="{{ route('admin.settings.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pengaturan</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kunci</th>
                        <th>Nilai</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                        <tr>
                            <td><code>{{ $setting->setting_key }}</code></td>
                            <td>{{ Str::limit($setting->setting_value, 60) }}</td>
                            <td><span class="admin-badge neutral">{{ $setting->setting_type }}</span></td>
                            <td>{{ Str::limit($setting->description, 50) }}</td>
                            <td>
                                <a href="{{ route('admin.settings.edit', $setting) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengaturan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pengaturan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
