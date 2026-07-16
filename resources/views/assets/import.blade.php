@extends('layouts.app')
@section('title', 'Import Data Aset dari Excel')
@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="text-muted">
            Upload file Excel (.xlsx) atau CSV berisi data aset. Baris pertama harus berisi nama
            kolom berikut (huruf kecil semua, pakai underscore):
        </p>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
                <thead><tr>
                    <th>kode_barang</th><th>kode_umum</th><th>kode_aset</th><th>nama_barang</th>
                    <th>kategori</th><th>tempat</th><th>tahun_pembelian</th><th>dana_pembelian</th><th>keterangan</th>
                </tr></thead>
                <tbody><tr>
                    <td class="text-muted">boleh kosong</td><td>LPX</td><td>001</td><td>Laptop Asus X441</td>
                    <td>Laptop</td><td>Lab Komputer</td><td>2023</td><td>Bosda</td><td class="text-muted">opsional</td>
                </tr></tbody>
            </table>
        </div>
        <ul class="small text-muted">
            <li><b>kode_barang</b> boleh dikosongkan — akan digenerate otomatis secara berurutan.</li>
            <li><b>kategori</b>, <b>tempat</b>, dan <b>dana_pembelian</b> diisi dengan nama-nya. Kalau nama tersebut belum ada di master data, sistem akan membuatkannya otomatis.</li>
            <li>Baris dengan <b>kode_umum + kode_aset</b> yang sudah ada akan dilewati (tidak dobel).</li>
        </ul>

        <a href="{{ route('assets.import.template') }}" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-download"></i> Unduh Template CSV
        </a>

        <form method="POST" action="{{ route('assets.import.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Pilih File (.xlsx / .xls / .csv, maks 5MB)</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <button class="btn btn-success"><i class="bi bi-upload"></i> Import Sekarang</button>
            <a href="{{ route('assets.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
