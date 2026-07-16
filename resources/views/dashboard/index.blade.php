@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Total Aset</div>
            <div class="fs-3 fw-bold">{{ $totalAset }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Kondisi Baik</div>
            <div class="fs-3 fw-bold text-success">{{ $asetBaik }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Rusak / Dalam Perbaikan</div>
            <div class="fs-3 fw-bold text-danger">{{ $asetRusak }} / {{ $asetDalamPerbaikan }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Sedang Dipinjam</div>
            <div class="fs-3 fw-bold text-primary">{{ $sedangDipinjam }}</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Riwayat Kerusakan Terbaru</div>
            <ul class="list-group list-group-flush">
                @forelse($riwayatTerbaru as $r)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $r->asset->nama_barang ?? '-' }}</span>
                        <span class="badge badge-status-{{ $r->status }}">{{ $r->status }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada riwayat.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Peminjaman Terbaru</div>
            <ul class="list-group list-group-flush">
                @forelse($peminjamanTerbaru as $p)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $p->transaction_code }} - {{ $p->borrower->name ?? '-' }}</span>
                        <span class="badge bg-secondary">{{ $p->status }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada peminjaman.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
