@extends('layouts.app')
@section('title', 'History Perbaikan')
@section('content')

<div class="d-flex justify-content-between mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/kode barang...">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="rusak" {{ request('status')=='rusak'?'selected':'' }}>Rusak</option>
            <option value="dalam_perbaikan" {{ request('status')=='dalam_perbaikan'?'selected':'' }}>Dalam Perbaikan</option>
            <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
        </select>
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('repairs.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> Catat Kerusakan</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr><th>Barang</th><th>Tgl Kerusakan</th><th>Tgl Perbaikan</th><th>Tgl Selesai</th><th>Status</th><th width="120">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($repairs as $r)
                <tr>
                    <td>{{ $r->asset->nama_barang ?? '-' }} <br><small class="text-muted">{{ $r->asset->kode_barang ?? '' }}</small></td>
                    <td>{{ $r->tanggal_kerusakan }}</td>
                    <td>{{ $r->tanggal_perbaikan ?? '-' }}</td>
                    <td>{{ $r->tanggal_selesai_perbaikan ?? '-' }}</td>
                    <td><span class="badge badge-status-{{ $r->status == 'selesai' ? 'baik' : $r->status }}">{{ $r->status }}</span></td>
                    <td>
                        <a href="{{ route('repairs.edit', $r) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('repairs.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus riwayat ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada riwayat kerusakan.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $repairs->links() }}
    </div>
</div>
@endsection
