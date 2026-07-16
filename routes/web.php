<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundingSourceController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RepairHistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ================= AUTH =================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::redirect('/', '/dashboard');

// ================= AUTHENTICATED =================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kategori Aset (tree)
    Route::middleware('permission:kategori')->prefix('kategori')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Lokasi/Tempat
    Route::middleware('permission:lokasi')->prefix('lokasi')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
    });

    // Dana Pembelian
    Route::middleware('permission:dana')->prefix('dana-pembelian')->name('funding_sources.')->group(function () {
        Route::get('/', [FundingSourceController::class, 'index'])->name('index');
        Route::post('/', [FundingSourceController::class, 'store'])->name('store');
        Route::put('/{funding_source}', [FundingSourceController::class, 'update'])->name('update');
        Route::delete('/{funding_source}', [FundingSourceController::class, 'destroy'])->name('destroy');
    });

    // Manajemen Aset
    Route::middleware('permission:aset')->prefix('aset')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/tambah', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/import', [AssetController::class, 'importForm'])->name('import.form');
        Route::post('/import', [AssetController::class, 'importStore'])->name('import.store');
        Route::get('/import/template', [AssetController::class, 'downloadTemplate'])->name('import.template');
        Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
        Route::get('/{asset}/ubah', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
    });

    // History Perbaikan / Kerusakan
    Route::middleware('permission:kerusakan')->prefix('kerusakan')->name('repairs.')->group(function () {
        Route::get('/', [RepairHistoryController::class, 'index'])->name('index');
        Route::get('/tambah', [RepairHistoryController::class, 'create'])->name('create');
        Route::post('/', [RepairHistoryController::class, 'store'])->name('store');
        Route::get('/{repair}/ubah', [RepairHistoryController::class, 'edit'])->name('edit');
        Route::put('/{repair}', [RepairHistoryController::class, 'update'])->name('update');
        Route::delete('/{repair}', [RepairHistoryController::class, 'destroy'])->name('destroy');
    });

    // Data Guru/Siswa (peminjam)
    Route::middleware('permission:peminjam')->prefix('peminjam')->name('borrowers.')->group(function () {
        Route::get('/', [BorrowerController::class, 'index'])->name('index');
        Route::post('/', [BorrowerController::class, 'store'])->name('store');
        Route::get('/import', [BorrowerController::class, 'importForm'])->name('import.form');
        Route::post('/import', [BorrowerController::class, 'importStore'])->name('import.store');
        Route::get('/import/template', [BorrowerController::class, 'downloadTemplate'])->name('import.template');
        Route::put('/{borrower}', [BorrowerController::class, 'update'])->name('update');
        Route::delete('/{borrower}', [BorrowerController::class, 'destroy'])->name('destroy');
    });
    Route::middleware('permission:peminjaman')->get('/peminjam/cari', [BorrowerController::class, 'search'])->name('borrowers.search');

    // Peminjaman (loan cart + checkout)
    Route::middleware('permission:peminjaman')->prefix('peminjaman')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/keranjang', [LoanController::class, 'cart'])->name('cart');
        Route::post('/keranjang/tambah', [LoanController::class, 'addToCart'])->name('cart.add');
        Route::post('/keranjang/hapus', [LoanController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/keranjang/pilih-peminjam', [LoanController::class, 'chooseBorrower'])->name('cart.choose_borrower');
        Route::post('/keranjang/kosongkan', [LoanController::class, 'clearCart'])->name('cart.clear');
        Route::post('/checkout', [LoanController::class, 'checkout'])->name('checkout');
        Route::get('/cari-aset', [LoanController::class, 'searchAssets'])->name('search_assets');
        Route::get('/{loan}', [LoanController::class, 'show'])->name('show');
        Route::post('/{loan}/kembalikan', [LoanController::class, 'returnAll'])->name('return_all');
        Route::post('/item/{loanItem}/kembalikan', [LoanController::class, 'returnItem'])->name('return_item');
    });

    // Manajemen User (superadmin only)
    Route::middleware('permission:user')->prefix('user')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/tambah', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/ubah', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});
