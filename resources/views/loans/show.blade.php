@extends('layouts.app')
@section('title', 'Detail Peminjaman ' . $loan->transaction_code)
@section('content')

<div class="row g-3">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5>{{ $loan->transaction_code }}</h5>
                <span class="badge {{ $loan->status == 'dipinjam' ? 'bg-warning text-dark' : ($loan->status == 'terlambat' ? 'bg-danger' : 'bg-success') }} mb-2">{{ $loan->status }}</span>
                <table class="table table-borderless mb-0">
                    <tr><th width="160">Peminjam</th><td>{{ $loan->borrower->name ?? '-' }} ({{ ucfirst($loan->borrower->type ?? '') }})</td></tr>
                    <tr><th>NIP/NIS</th><td>{{ $loan->borrower->identity_number ?? '-' }}</td></tr>
                    <tr><th>Kelas/Jabatan</th><td>{{ $loan->borrower->unit ?? '-' }}</td></tr>
                    <tr><th>Dilayani oleh</th><td>{{ $loan->petugas->name ?? '-' }}</td></tr>
                    <tr><th>Tgl Pinjam</th><td>{{ $loan->tanggal_pinjam }}</td></tr>
                    <tr><th>Rencana Kembali</th><td>{{ $loan->tanggal_kembali_rencana ?? '-' }}</td></tr>
                    <tr><th>Tgl Kembali Aktual</th><td>{{ $loan->tanggal_kembali_aktual ?? '-' }}</td></tr>
                    <tr><th>Keterangan</th><td>{{ $loan->keterangan ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Daftar Barang</span>
                @if($loan->status == 'dipinjam')
                <form action="{{ route('loans.return_all', $loan) }}" method="POST" onsubmit="return confirm('Tandai semua barang sudah dikembalikan?')">
                    @csrf
                    <button class="btn btn-sm btn-success">Kembalikan Semua</button>
                </form>
                @endif
            </div>
            <ul class="list-group list-group-flush">
                @foreach($loan->items as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div>{{ $item->asset->nama_barang ?? '-' }}</div>
                        <small class="text-muted">{{ $item->asset->kode_barang ?? '' }} | Kondisi pinjam: {{ $item->kondisi_pinjam }}</small>
                        @if($item->is_returned)
                            <div class="small text-success">Dikembalikan {{ $item->returned_at }} - kondisi: {{ $item->kondisi_kembali }}</div>
                        @endif
                    </div>
                    @if(!$item->is_returned)
                    <form action="{{ route('loans.return_item', $item) }}" method="POST" class="d-flex gap-1">
                        @csrf
                        <select name="kondisi_kembali" class="form-select form-select-sm">
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                        </select>
                        <button class="btn btn-sm btn-outline-success">Kembalikan</button>
                    </form>
                    @else
                        <span class="badge bg-success">Kembali</span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
