@extends('layouts.app')
@section('title', 'Data Guru/Siswa')
@section('content')

<div class="d-flex justify-content-between mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/NIP/NIS...">
        <select name="type" class="form-select">
            <option value="">Semua</option>
            <option value="guru" {{ request('type')=='guru'?'selected':'' }}>Guru</option>
            <option value="siswa" {{ request('type')=='siswa'?'selected':'' }}>Siswa</option>
        </select>
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <div>
        <a href="{{ route('loans.cart') }}" class="btn btn-primary"><i class="bi bi-cart"></i> Buat Peminjaman</a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Tambah</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Tipe</th><th>Nama</th><th>NIP/NIS</th><th>Kelas/Jabatan</th><th>Telp</th><th width="180">Aksi</th></tr></thead>
            <tbody>
                @forelse($borrowers as $b)
                <tr>
                    <td><span class="badge {{ $b->type == 'guru' ? 'bg-info' : 'bg-secondary' }}">{{ ucfirst($b->type) }}</span></td>
                    <td>{{ $b->name }}</td>
                    <td>{{ $b->identity_number }}</td>
                    <td>{{ $b->unit }}</td>
                    <td>{{ $b->phone }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="{{ $b->id }}" data-type="{{ $b->type }}" data-name="{{ $b->name }}"
                            data-idn="{{ $b->identity_number }}" data-unit="{{ $b->unit }}" data-phone="{{ $b->phone }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('loans.cart.choose_borrower') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="borrower_id" value="{{ $b->id }}">
                            <button class="btn btn-sm btn-outline-primary" title="Pinjamkan barang ke orang ini"><i class="bi bi-box-arrow-in-down"></i></button>
                        </form>
                        <form action="{{ route('borrowers.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $borrowers->links() }}
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('borrowers.store') }}" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Tambah Guru/Siswa</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select" required>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">NIP/NIS</label><input type="text" name="identity_number" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Kelas / Jabatan-Mapel</label><input type="text" name="unit" class="form-control"></div>
                <div class="mb-3"><label class="form-label">No. Telp</label><input type="text" name="phone" class="form-control"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button><button class="btn btn-success">Simpan</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editForm" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Ubah Data</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" id="editType" class="form-select" required>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="name" id="editName" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">NIP/NIS</label><input type="text" name="identity_number" id="editIdn" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Kelas / Jabatan-Mapel</label><input type="text" name="unit" id="editUnit" class="form-control"></div>
                <div class="mb-3"><label class="form-label">No. Telp</label><input type="text" name="phone" id="editPhone" class="form-control"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button><button class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
    const b = event.relatedTarget;
    document.getElementById('editType').value = b.getAttribute('data-type');
    document.getElementById('editName').value = b.getAttribute('data-name');
    document.getElementById('editIdn').value = b.getAttribute('data-idn');
    document.getElementById('editUnit').value = b.getAttribute('data-unit');
    document.getElementById('editPhone').value = b.getAttribute('data-phone');
    document.getElementById('editForm').action = '/peminjam/' + b.getAttribute('data-id');
});
</script>
@endsection
