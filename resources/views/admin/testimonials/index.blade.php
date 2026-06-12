@extends('admin.layout')

@section('title', 'Testimoni')
@section('heading', 'Kelola Testimoni')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $testimonials->total() }} testimoni ditemukan</span>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Testimoni</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Peran</th>
                        <th>Rating</th>
                        <th>Teks</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testimonials as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td><img src="{{ $t->image }}" alt="{{ $t->name }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"></td>
                            <td>{{ $t->name }}</td>
                            <td>{{ $t->role }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $t->rating ? '-fill text-warning' : '' }}" style="font-size:.8rem;"></i>
                                @endfor
                            </td>
                            <td>{{ Str::limit($t->text, 60) }}</td>
                            <td>
                                <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus testimoni ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada testimoni.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $testimonials->links() }}</div>
@endsection
