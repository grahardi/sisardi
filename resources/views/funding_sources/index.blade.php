@extends('layouts.app')
@section('title', 'Dana Pembelian')
@section('content')

<div class="mb-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Tambah Sumber Dana</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Nama Sumber Dana</th><th>Keterangan</th><th width="120">Aksi</th></tr></thead>
            <tbody>
                @forelse($fundingSources as $f)
                <tr>
                    <td>{{ $f->name }}</td>
                    <td>{{ $f->keterangan }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="{{ $f->id }}" data-name="{{ $f->name }}" data-ket="{{ $f->keterangan }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('funding_sources.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sumber dana ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-muted text-center">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $fundingSources->links() }}
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('funding_sources.store') }}" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Tambah Sumber Dana</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nama Sumber Dana</label><input type="text" name="name" class="form-control" required placeholder="Contoh: Bosda, Bos Pusat, Komite"></div>
                <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button><button class="btn btn-success">Simpan</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editForm" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Ubah Sumber Dana</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nama Sumber Dana</label><input type="text" name="name" id="editName" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" id="editKet" class="form-control"></textarea></div>
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
    document.getElementById('editName').value = b.getAttribute('data-name');
    document.getElementById('editKet').value = b.getAttribute('data-ket');
    document.getElementById('editForm').action = '/dana-pembelian/' + b.getAttribute('data-id');
});
</script>
@endsection
