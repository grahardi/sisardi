<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\RepairHistory;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAset = Asset::count();
        $asetBaik = Asset::where('status', 'baik')->count();
        $asetRusak = Asset::where('status', 'rusak')->count();
        $asetDalamPerbaikan = Asset::where('status', 'dalam_perbaikan')->count();
        $sedangDipinjam = \App\Models\LoanItem::where('is_returned', false)->count();
        $peminjamanAktif = Loan::where('status', 'dipinjam')->count();
        $riwayatTerbaru = RepairHistory::with('asset')->orderByDesc('id')->limit(5)->get();
        $peminjamanTerbaru = Loan::with(['borrower', 'items'])->orderByDesc('id')->limit(5)->get();

        return view('dashboard.index', compact(
            'totalAset', 'asetBaik', 'asetRusak', 'asetDalamPerbaikan',
            'sedangDipinjam', 'peminjamanAktif', 'riwayatTerbaru', 'peminjamanTerbaru'
        ));
    }
}
