@extends('admin.layout')

@section('title', 'Galeri')
@section('heading', 'Kelola Galeri')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $galleries->total() }} foto ditemukan</span>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Foto</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($galleries as $gallery)
                        <tr>
                            <td>{{ $gallery->id }}</td>
                            <td><img src="{{ $gallery->image }}" alt="{{ $gallery->title }}" style="max-height:50px;border-radius:6px;"></td>
                            <td>{{ $gallery->title }}</td>
                            <td>{{ $gallery->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada foto.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $galleries->links() }}</div>
@endsection
