@extends('layouts.app')
@section('title', 'Detail Aset')
@section('content')
<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                @if($asset->foto)
                    <img src="{{ $asset->foto_url }}" alt="Foto {{ $asset->nama_barang }}" class="img-fluid rounded mb-3" style="max-height:250px;object-fit:cover;">
                @else
                    <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded mb-3" style="height:150px;">
                        <i class="bi bi-image fs-1"></i>
                    </div>
                @endif
                <h5>{{ $asset->nama_barang }}</h5>
                <span class="badge badge-status-{{ $asset->status }} mb-2">{{ str_replace('_',' ',$asset->status) }}</span>
                <table class="table table-borderless mb-0">
                    <tr><th width="180">Kode Barang</th><td>{{ $asset->kode_barang }}</td></tr>
                    <tr><th>Kode Umum</th><td>{{ $asset->kode_umum }}</td></tr>
                    <tr><th>Kode Aset</th><td>{{ $asset->kode_aset }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $asset->category->name ?? '-' }}</td></tr>
                    <tr><th>Tempat</th><td>{{ $asset->location->name ?? '-' }}</td></tr>
                    <tr><th>Tahun Pembelian</th><td>{{ $asset->tahun_pembelian }}</td></tr>
                    <tr><th>Dana Pembelian</th><td>{{ $asset->fundingSource->name ?? '-' }}</td></tr>
                    <tr><th>Keterangan</th><td>{{ $asset->keterangan }}</td></tr>
                </table>
                <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary mt-2">Ubah Data</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm" id="printArea">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-qr-code me-1"></i> Label QR Barang</div>
            <div class="card-body text-center">
                <div id="qrCode" class="d-flex justify-content-center mb-2"></div>
                <div class="fw-semibold">{{ $asset->kode_barang }}</div>
                <div class="small text-muted">{{ $asset->nama_barang }}</div>
                <button class="btn btn-sm btn-outline-secondary mt-2 no-print" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Label
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Riwayat Perbaikan</div>
            <ul class="list-group list-group-flush">
                @forelse($asset->repairHistories as $r)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <span>Rusak: {{ $r->tanggal_kerusakan }}</span>
                        <span class="badge badge-status-{{ $r->status == 'selesai' ? 'baik' : $r->status }}">{{ $r->status }}</span>
                    </div>
                    <small class="text-muted">Perbaikan: {{ $r->tanggal_perbaikan ?? '-' }} | Selesai: {{ $r->tanggal_selesai_perbaikan ?? '-' }}</small>
                    <div class="small">{{ $r->keterangan_kerusakan }}</div>
                </li>
                @empty
                <li class="list-group-item text-muted">Belum ada riwayat perbaikan.</li>
                @endforelse
            </ul>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Riwayat Peminjaman</div>
            <ul class="list-group list-group-flush">
                @forelse($asset->loanItems as $li)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $li->loan->borrower->name ?? '-' }} ({{ $li->loan->transaction_code ?? '-' }})</span>
                    <span class="badge {{ $li->is_returned ? 'bg-success' : 'bg-warning text-dark' }}">{{ $li->is_returned ? 'Kembali' : 'Dipinjam' }}</span>
                </li>
                @empty
                <li class="list-group-item text-muted">Belum pernah dipinjam.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; top: 0; left: 0; width: 100%; border: none !important; box-shadow: none !important; }
    .no-print { display: none !important; }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById('qrCode'), {
    text: '{{ $asset->kode_barang }}',
    width: 150,
    height: 150,
});
</script>
@endsection
