@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')

<div class="mb-3">
    <a href="{{ route('users.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> Tambah User</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Hak Akses</th><th width="120">Aksi</th></tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge {{ $u->role == 'superadmin' ? 'bg-dark' : 'bg-primary' }}">{{ $u->role }}</span></td>
                    <td>
                        @if($u->isSuperadmin())
                            <span class="text-muted">Semua fitur</span>
                        @else
                            @foreach(($u->permissions ?? []) as $p)
                                <span class="badge bg-light text-dark border">{{ $availablePermissions[$p] ?? $p }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>
@endsection
