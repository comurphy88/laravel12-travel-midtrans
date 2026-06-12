@extends('admin.layout')

@section('title', 'Destinasi Wisata')
@section('heading', 'Kelola Destinasi Wisata')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $destinations->total() }} destinasi ditemukan</span>
        <button class="btn btn-primary" onclick="openModal()"><i class="bi bi-plus-lg"></i> Tambah Destinasi</button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $dest)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($dest->image)
                                        <img src="{{ $dest->image }}" alt="" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                                    @endif
                                    <strong>{{ $dest->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $dest->location }}</td>
                            <td>Rp {{ number_format($dest->price, 0, ',', '.') }}</td>
                            <td><i class="bi bi-star-fill text-warning"></i> {{ $dest->rating }}</td>
                            <td><span class="admin-badge {{ $dest->active ? 'success' : 'neutral' }}">{{ $dest->active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" onclick="openModal({{ $dest->id }}, {{ Js::from($dest) }})"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.destinations.destroy', $dest) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus destinasi {{ $dest->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada destinasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $destinations->links() }}</div>

    {{-- Modal Tambah / Edit Destinasi --}}
    <div class="modal fade" id="modalDestination" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formDestination" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Destinasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Destinasi</label>
                                <input type="text" name="name" id="fName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="location" id="fLocation" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" id="fDescription" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">URL Gambar</label>
                                <input type="url" name="image" id="fImage" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" name="price" id="fPrice" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rating</label>
                                <input type="number" name="rating" id="fRating" class="form-control" min="0" max="5" step="0.1" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="active" id="fActive" value="1" checked>
                                    <label class="form-check-label" for="fActive">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openModal(id = null, data = null) {
        const form = document.getElementById('formDestination');
        const title = document.getElementById('modalTitle');
        const method = document.getElementById('formMethod');

        form.reset();
        document.getElementById('fActive').checked = true;

        if (id && data) {
            title.textContent = 'Edit Destinasi';
            form.action = '{{ url("admin/destinations") }}/' + id;
            method.value = 'PUT';
            document.getElementById('fName').value = data.name;
            document.getElementById('fLocation').value = data.location;
            document.getElementById('fDescription').value = data.description;
            document.getElementById('fImage').value = data.image;
            document.getElementById('fPrice').value = data.price;
            document.getElementById('fRating').value = data.rating;
            document.getElementById('fActive').checked = !!data.active;
        } else {
            title.textContent = 'Tambah Destinasi';
            form.action = '{{ route("admin.destinations.store") }}';
            method.value = 'POST';
        }

        new bootstrap.Modal(document.getElementById('modalDestination')).show();
    }
</script>
@endsection
