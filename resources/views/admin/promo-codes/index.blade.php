@extends('admin.layout')

@section('title', 'Kode Promo')
@section('heading', 'Kelola Kode Promo')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">{{ $promoCodes->total() }} kode promo ditemukan</span>
        <a href="{{ route('admin.promo-codes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Promo</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        <th>Min. Pembelian</th>
                        <th>Penggunaan</th>
                        <th>Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promoCodes as $promo)
                        <tr>
                            <td><code>{{ $promo->code }}</code></td>
                            <td>{{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Nominal' }}</td>
                            <td>
                                @if($promo->discount_type === 'percentage')
                                    {{ $promo->discount_value }}%
                                @else
                                    Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}</td>
                            <td>{{ $promo->used_count }} / {{ $promo->usage_limit ?: '∞' }}</td>
                            <td>
                                @if($promo->valid_from && $promo->valid_until)
                                    {{ $promo->valid_from->format('d M Y') }} - {{ $promo->valid_until->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="admin-badge {{ $promo->active ? 'success' : 'neutral' }}">{{ $promo->active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.promo-codes.destroy', $promo) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kode promo ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada kode promo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $promoCodes->links() }}</div>
@endsection
