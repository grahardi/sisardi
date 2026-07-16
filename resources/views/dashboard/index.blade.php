@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="row g-3 mb-3">
    <div class="col-lg-3 col-6">
        <div class="small-box text-white shadow" style="background: linear-gradient(135deg,#6a11cb,#8e2de2);">
            <div class="inner p-3">
                <h3 class="mb-0">{{ $totalAset }}</h3>
                <p class="mb-0">Total Aset</p>
            </div>
            <i class="bi bi-box-seam icon position-absolute" style="right:15px; top:15px; font-size:3rem;"></i>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box text-white shadow" style="background: linear-gradient(135deg,#11998e,#38ef7d);">
            <div class="inner p-3">
                <h3 class="mb-0">{{ $asetBaik }}</h3>
                <p class="mb-0">Kondisi Baik</p>
            </div>
            <i class="bi bi-check-circle icon position-absolute" style="right:15px; top:15px; font-size:3rem;"></i>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box text-white shadow" style="background: linear-gradient(135deg,#f2994a,#f2c94c);">
            <div class="inner p-3">
                <h3 class="mb-0">{{ $asetRusak }} / {{ $asetDalamPerbaikan }}</h3>
                <p class="mb-0">Rusak / Dalam Perbaikan</p>
            </div>
            <i class="bi bi-tools icon position-absolute" style="right:15px; top:15px; font-size:3rem;"></i>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box text-white shadow" style="background: linear-gradient(135deg,#2575fc,#00c6ff);">
            <div class="inner p-3">
                <h3 class="mb-0">{{ $sedangDipinjam }}</h3>
                <p class="mb-0">Sedang Dipinjam</p>
            </div>
            <i class="bi bi-arrow-left-right icon position-absolute" style="right:15px; top:15px; font-size:3rem;"></i>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold border-bottom border-3 border-warning">
                <i class="bi bi-tools text-warning me-1"></i> Riwayat Kerusakan Terbaru
            </div>
            <ul class="list-group list-group-flush">
                @forelse($riwayatTerbaru as $r)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $r->asset->nama_barang ?? '-' }}</span>
                        <span class="badge badge-status-{{ $r->status == 'selesai' ? 'baik' : $r->status }}">{{ $r->status }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada riwayat.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold border-bottom border-3 border-info">
                <i class="bi bi-arrow-left-right text-info me-1"></i> Peminjaman Terbaru
            </div>
            <ul class="list-group list-group-flush">
                @forelse($peminjamanTerbaru as $p)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $p->transaction_code }} - {{ $p->borrower->name ?? '-' }}</span>
                        <span class="badge {{ $p->status == 'dipinjam' ? 'bg-warning text-dark' : 'bg-success' }}">{{ $p->status }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada peminjaman.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
