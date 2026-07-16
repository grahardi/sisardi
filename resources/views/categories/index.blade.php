@extends('layouts.app')
@section('title', 'Kategori Aset')
@section('content')

<div class="mb-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal" data-parent="" data-parentname="">
        <i class="bi bi-plus-lg"></i> Tambah Kategori Utama
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="tree">
            <ul>
                @forelse($tree as $node)
                    @include('categories._tree_node', ['node' => $node])
                @empty
                    <li class="text-muted">Belum ada kategori.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('categories.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori <span id="addParentLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="parent_id" id="addParentId">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                <button class="btn btn-success" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editCategoryForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Ubah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Induk Kategori</label>
                    <select name="parent_id" id="editParentId" class="form-select">
                        <option value="">-- Tanpa Induk (Kategori Utama) --</option>
                        @foreach($allCategories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('addCategoryModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const parentId = button.getAttribute('data-parent');
    const parentName = button.getAttribute('data-parentname');
    document.getElementById('addParentId').value = parentId || '';
    document.getElementById('addParentLabel').textContent = parentName ? ('di dalam "' + parentName + '"') : '';
});

document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const parent = button.getAttribute('data-parent');
    document.getElementById('editName').value = name;
    document.getElementById('editParentId').value = parent && parent !== '' ? parent : '';
    document.getElementById('editCategoryForm').action = '/kategori/' + id;
});
</script>
@endsection
