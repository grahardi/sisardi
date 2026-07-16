@extends('layouts.app')
@section('title', 'Manajemen Aset')
@section('content')

<div class="d-flex justify-content-between mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/kode barang...">
        <select name="category_id" class="form-select">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="baik" {{ request('status')=='baik'?'selected':'' }}>Baik</option>
            <option value="rusak" {{ request('status')=='rusak'?'selected':'' }}>Rusak</option>
            <option value="dalam_perbaikan" {{ request('status')=='dalam_perbaikan'?'selected':'' }}>Dalam Perbaikan</option>
        </select>
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <div>
        <a href="{{ route('assets.import.form') }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel"></i> Import Excel</a>
        <a href="{{ route('assets.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> Tambah Aset</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Kode Barang</th><th>Kode Umum</th><th>Kode Aset</th><th>Nama Barang</th>
                    <th>Kategori</th><th>Tempat</th><th>Tahun</th><th>Status</th><th width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $a)
                <tr>
                    <td>{{ $a->kode_barang }}</td>
                    <td>{{ $a->kode_umum }}</td>
                    <td>{{ $a->kode_aset }}</td>
                    <td><a href="{{ route('assets.show', $a) }}">{{ $a->nama_barang }}</a></td>
                    <td>{{ $a->category->name ?? '-' }}</td>
                    <td>{{ $a->location->name ?? '-' }}</td>
                    <td>{{ $a->tahun_pembelian }}</td>
                    <td><span class="badge badge-status-{{ $a->status }}">{{ str_replace('_',' ',$a->status) }}</span></td>
                    <td>
                        <a href="{{ route('assets.edit', $a) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('assets.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aset ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted">Belum ada data aset.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $assets->links() }}
    </div>
</div>
@endsection
