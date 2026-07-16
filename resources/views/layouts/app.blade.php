<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - SiSardi</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">

    <style>
        :root {
            --sisardi-1: #6a11cb;
            --sisardi-2: #2575fc;
        }
        .app-sidebar {
            background: #1a1d21 !important;
        }
        .app-sidebar .nav-link { color: rgba(255,255,255,.75); }
        .app-sidebar .nav-link:hover, .app-sidebar .nav-link.active {
            background: rgba(255,255,255,.08);
            color: #fff;
        }
        .app-sidebar .brand-link { border-bottom: 1px solid rgba(255,255,255,.1); }
        .app-sidebar .brand-text { font-weight: 700; }
        .app-sidebar .user-panel .info small { color: rgba(255,255,255,.6); }

        .icon-dash { width: 30px; text-align: center; }
        .badge-status-baik { background:#00a65a; }
        .badge-status-rusak { background:#dd4b39; }
        .badge-status-dalam_perbaikan { background:#f39c12; }

        .small-box { border-radius: .75rem; overflow:hidden; }
        .small-box .icon { opacity:.35; }

        .tree ul { list-style: none; padding-left: 1.25rem; }
        .tree > ul { padding-left: 0; }

        .icon-picker .icon-option { width: 46px; height: 46px; font-size: 1.1rem; }
        .icon-picker .icon-option.active {
            background: linear-gradient(135deg, var(--sisardi-1), var(--sisardi-2));
            color: #fff;
            border-color: transparent;
        }
    </style>
    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

    <!-- NAVBAR -->
    <nav class="app-header navbar navbar-expand bg-white shadow-sm">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list fs-4"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                @php $navUser = auth()->user(); @endphp
                <li class="nav-item me-2 d-none d-md-block">
                    <span class="fw-semibold">{{ $navUser->name }}</span>
                    <span class="badge {{ $navUser->role === 'superadmin' ? 'bg-dark' : 'bg-primary' }} ms-1">
                        {{ $navUser->role === 'superadmin' ? 'Super Admin' : 'Petugas' }}
                    </span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <aside class="app-sidebar shadow" data-bs-theme="dark">
        <div class="sidebar-brand d-flex align-items-center px-3 py-3">
            <i class="bi bi-building fs-4 me-2"></i>
            <span class="brand-text">SiSardi</span>
        </div>
        <div class="px-3 pb-2 small text-white-50">SMP Negeri 1 Turen</div>

        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" role="menu">
                    @php $user = auth()->user(); @endphp

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2 icon-dash text-warning"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    @if($user->hasPermission('kategori'))
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-diagram-3 icon-dash text-info"></i>
                            <p>Kategori Aset</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('aset'))
                    <li class="nav-item">
                        <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-box-seam icon-dash text-success"></i>
                            <p>Manajemen Aset</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('dana'))
                    <li class="nav-item">
                        <a href="{{ route('funding_sources.index') }}" class="nav-link {{ request()->routeIs('funding_sources.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-cash-coin icon-dash text-warning"></i>
                            <p>Dana Pembelian</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('lokasi'))
                    <li class="nav-item">
                        <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-geo-alt icon-dash text-danger"></i>
                            <p>Lokasi/Tempat</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('kerusakan'))
                    <li class="nav-item">
                        <a href="{{ route('repairs.index') }}" class="nav-link {{ request()->routeIs('repairs.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-tools icon-dash" style="color:#f39c12"></i>
                            <p>History Perbaikan</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('peminjaman'))
                    <li class="nav-item">
                        <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-arrow-left-right icon-dash text-info"></i>
                            <p>Peminjaman</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('peminjam'))
                    <li class="nav-item">
                        <a href="{{ route('borrowers.index') }}" class="nav-link {{ request()->routeIs('borrowers.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people icon-dash text-light"></i>
                            <p>Data Guru/Siswa</p>
                        </a>
                    </li>
                    @endif

                    @if($user->hasPermission('user'))
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-gear icon-dash text-secondary"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('title', 'Dashboard')</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
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
    </main>

    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">SiSardi &copy; {{ date('Y') }}</div>
        <strong>SMP Negeri 1 Turen</strong> - Sistem Sarpras Digital
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/js/adminlte.min.js"></script>
@yield('scripts')
</body>
</html>
