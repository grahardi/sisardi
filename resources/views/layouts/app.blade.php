<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SiSardi') - SMP Negeri 1 Turen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #1e2a38; color: #fff; width: 250px; }
        .sidebar a { color: #cfd8e3; text-decoration: none; display: block; padding: .6rem 1rem; border-radius: .35rem; }
        .sidebar a.active, .sidebar a:hover { background: #2c3e50; color: #fff; }
        .sidebar .brand { font-weight: 700; font-size: 1.15rem; padding: 1rem; color: #fff; }
        .wrapper { display: flex; }
        .content { flex: 1; padding: 1.5rem; }
        .badge-status-baik { background:#198754; }
        .badge-status-rusak { background:#dc3545; }
        .badge-status-dalam_perbaikan { background:#fd7e14; }
        .tree ul { list-style: none; padding-left: 1.25rem; }
        .tree > ul { padding-left: 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <nav class="sidebar d-flex flex-column p-2">
        <div class="brand"><i class="bi bi-building"></i> SiSardi</div>
        <div class="px-3 pb-2 small text-secondary">SMP Negeri 1 Turen</div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>

        @php $user = auth()->user(); @endphp

        @if($user->hasPermission('kategori'))
        <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="bi bi-diagram-3 me-2"></i>Kategori Aset</a>
        @endif

        @if($user->hasPermission('aset'))
        <a href="{{ route('assets.index') }}" class="{{ request()->routeIs('assets.*') ? 'active' : '' }}"><i class="bi bi-box-seam me-2"></i>Manajemen Aset</a>
        @endif

        @if($user->hasPermission('dana'))
        <a href="{{ route('funding_sources.index') }}" class="{{ request()->routeIs('funding_sources.*') ? 'active' : '' }}"><i class="bi bi-cash-coin me-2"></i>Dana Pembelian</a>
        @endif

        @if($user->hasPermission('lokasi'))
        <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.*') ? 'active' : '' }}"><i class="bi bi-geo-alt me-2"></i>Lokasi/Tempat</a>
        @endif

        @if($user->hasPermission('kerusakan'))
        <a href="{{ route('repairs.index') }}" class="{{ request()->routeIs('repairs.*') ? 'active' : '' }}"><i class="bi bi-tools me-2"></i>History Perbaikan</a>
        @endif

        @if($user->hasPermission('peminjaman'))
        <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right me-2"></i>Peminjaman</a>
        @endif

        @if($user->hasPermission('peminjam'))
        <a href="{{ route('borrowers.index') }}" class="{{ request()->routeIs('borrowers.*') ? 'active' : '' }}"><i class="bi bi-people me-2"></i>Data Guru/Siswa</a>
        @endif

        @if($user->hasPermission('user'))
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-person-gear me-2"></i>Manajemen User</a>
        @endif

        <div class="mt-auto p-2 border-top border-secondary">
            <div class="small mb-2">{{ $user->name }}<br><span class="text-secondary">{{ $user->role === 'superadmin' ? 'Super Admin' : 'Petugas' }}</span></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> Keluar</button>
            </form>
        </div>
    </nav>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
