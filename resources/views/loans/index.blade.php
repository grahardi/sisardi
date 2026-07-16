@extends('layouts.app')
@section('title', 'Peminjaman')
@section('content')

@if(request('returned'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="bi bi-check-circle-fill me-1"></i>
        Barang <b>{{ request('nama') }}</b> ({{ request('kode') }}) berhasil dikembalikan.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode transaksi/nama peminjam...">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="dipinjam" {{ request('status')=='dipinjam'?'selected':'' }}>Dipinjam</option>
            <option value="dikembalikan" {{ request('status')=='dikembalikan'?'selected':'' }}>Dikembalikan</option>
            <option value="terlambat" {{ request('status')=='terlambat'?'selected':'' }}>Terlambat</option>
        </select>
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('loans.cart') }}" class="btn btn-success"><i class="bi bi-cart-plus"></i> Buat Peminjaman Baru</a>
</div>
<div class="mb-3">
    <a href="{{ route('loans.quick_return') }}" class="btn btn-outline-primary"><i class="bi bi-upc-scan"></i> Pengembalian Cepat (Scan)</a>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Kode</th><th>Peminjam</th><th>Jumlah Barang</th><th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Status</th><th width="100">Aksi</th></tr></thead>
            <tbody>
                @forelse($loans as $loan)
                <tr>
                    <td>{{ $loan->transaction_code }}</td>
                    <td>{{ $loan->borrower->name ?? '-' }} <span class="badge bg-secondary">{{ ucfirst($loan->borrower->type ?? '') }}</span></td>
                    <td>{{ $loan->items->count() }}</td>
                    <td>{{ $loan->tanggal_pinjam }}</td>
                    <td>{{ $loan->tanggal_kembali_rencana ?? '-' }}</td>
                    <td><span class="badge {{ $loan->status == 'dipinjam' ? 'bg-warning text-dark' : ($loan->status == 'terlambat' ? 'bg-danger' : 'bg-success') }}">{{ $loan->status }}</span></td>
                    <td><a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada data peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $loans->links() }}
    </div>
</div>
@endsection
