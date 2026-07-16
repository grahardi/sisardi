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
                    <th width="60">Foto</th>
                    <th>Kode Barang</th><th>Kode Umum</th><th>Kode Aset</th><th>Nama Barang</th>
                    <th>Kategori</th><th>Tempat</th><th>Tahun</th><th>Status</th><th width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $a)
                <tr>
                    <td>
                        @if($a->foto)
                            <img src="{{ $a->foto_url }}" class="img-thumbnail" style="width:45px;height:45px;object-fit:cover;">
                        @else
                            <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded" style="width:45px;height:45px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $a->kode_barang }}</td>
                    <td>{{ $a->kode_umum }}</td>
                    <td>{{ $a->kode_aset }}</td>
                    <td><a href="{{ route('assets.show', $a) }}">{{ $a->nama_barang }}</a></td>
                    <td>{{ $a->category->name ?? '-' }}</td>
                    <td>{{ $a->location->name ?? '-' }}</td>
                    <td>{{ $a->tahun_pembelian }}</td>
                    <td><span class="badge badge-status-{{ $a->status }}">{{ str_replace('_',' ',$a->status) }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary btn-qr" title="Lihat QR" data-kode="{{ $a->kode_barang }}" data-nama="{{ $a->nama_barang }}"><i class="bi bi-qr-code"></i></button>
                        <a href="{{ route('assets.edit', $a) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('assets.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aset ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted">Belum ada data aset.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $assets->links() }}
    </div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h6 class="modal-title">QR Code Barang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qrModalCode" class="d-flex justify-content-center mb-2"></div>
                <div class="fw-semibold" id="qrModalKode"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
let qrModalInstance;
document.querySelectorAll('.btn-qr').forEach(btn => {
    btn.addEventListener('click', function () {
        const kode = this.getAttribute('data-kode');
        const nama = this.getAttribute('data-nama');
        document.getElementById('qrModalKode').textContent = kode + ' - ' + nama;
        const qrContainer = document.getElementById('qrModalCode');
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, { text: kode, width: 160, height: 160 });
        qrModalInstance = qrModalInstance || new bootstrap.Modal(document.getElementById('qrModal'));
        qrModalInstance.show();
    });
});
</script>
@endsection
