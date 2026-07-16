<li>
    <div class="d-flex align-items-center justify-content-between border rounded px-2 py-1 mb-1 bg-white">
        <span><i class="bi bi-folder2 text-warning me-1"></i>{{ $node->name }}</span>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                data-id="{{ $node->id }}" data-name="{{ $node->name }}" data-parent="{{ $node->parent_id }}">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                data-parent="{{ $node->id }}" data-parentname="{{ $node->name }}">
                <i class="bi bi-plus-lg"></i>
            </button>
            <form action="{{ route('categories.destroy', $node) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $node->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>
    @if($node->childrenRecursive->count())
        <ul>
            @foreach($node->childrenRecursive as $child)
                @include('categories._tree_node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
