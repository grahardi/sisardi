@extends('layouts.app')
@section('title', 'Import Data Guru/Siswa dari Excel')
@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="text-muted">
            Upload file Excel (.xlsx) atau CSV berisi data Guru atau Siswa. Pilih tipenya dulu,
            karena format template Guru dan Siswa sama tapi datanya dipisah per-tipe. Baris pertama
            file harus berisi kolom berikut:
        </p>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
                <thead><tr><th>nama</th><th>nip_nis</th><th>kelas_jabatan</th><th>telp</th></tr></thead>
                <tbody><tr>
                    <td>Contoh Nama</td><td>NIP untuk guru / NIS untuk siswa</td>
                    <td>Kelas (siswa) atau Jabatan/Mapel (guru)</td><td class="text-muted">opsional</td>
                </tr></tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('borrowers.import.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tipe Data</label>
                    <select name="type" id="typeSelect" class="form-select" required>
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('borrowers.import.template', ['type' => 'siswa']) }}" id="templateLink" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-download"></i> Unduh Template
                    </a>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih File (.xlsx / .xls / .csv, maks 5MB)</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <button class="btn btn-success"><i class="bi bi-upload"></i> Import Sekarang</button>
            <a href="{{ route('borrowers.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('typeSelect').addEventListener('change', function () {
    document.getElementById('templateLink').href = '{{ route("borrowers.import.template") }}?type=' + this.value;
});
</script>
@endsection
