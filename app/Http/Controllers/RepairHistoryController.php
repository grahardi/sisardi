<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RepairHistory;
use Illuminate\Http\Request;

class RepairHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = RepairHistory::with('asset');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('q')) {
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $repairs = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('repairs.index', compact('repairs'));
    }

    public function create()
    {
        $assets = Asset::orderBy('nama_barang')->get();
        return view('repairs.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'tanggal_kerusakan' => 'required|date',
            'tanggal_perbaikan' => 'nullable|date|after_or_equal:tanggal_kerusakan',
            'tanggal_selesai_perbaikan' => 'nullable|date|after_or_equal:tanggal_perbaikan',
            'keterangan_kerusakan' => 'nullable|string',
        ]);

        $repair = RepairHistory::create($data);
        $repair->syncStatus();

        return redirect()->route('repairs.index')->with('success', 'Riwayat kerusakan berhasil dicatat, status aset diperbarui.');
    }

    public function edit(RepairHistory $repair)
    {
        $assets = Asset::orderBy('nama_barang')->get();
        return view('repairs.edit', compact('repair', 'assets'));
    }

    public function update(Request $request, RepairHistory $repair)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'tanggal_kerusakan' => 'required|date',
            'tanggal_perbaikan' => 'nullable|date|after_or_equal:tanggal_kerusakan',
            'tanggal_selesai_perbaikan' => 'nullable|date|after_or_equal:tanggal_perbaikan',
            'keterangan_kerusakan' => 'nullable|string',
        ]);

        $repair->update($data);
        $repair->syncStatus();

        return redirect()->route('repairs.index')->with('success', 'Riwayat kerusakan berhasil diperbarui, status aset diperbarui.');
    }

    public function destroy(RepairHistory $repair)
    {
        $asset = $repair->asset;
        $repair->delete();

        // Setelah dihapus, kembalikan status aset berdasarkan riwayat perbaikan terakhir yang tersisa
        $latest = $asset->repairHistories()->first();
        $asset->status = $latest ? match ($latest->status) {
            'selesai' => 'baik',
            'dalam_perbaikan' => 'dalam_perbaikan',
            default => 'rusak',
        } : 'baik';
        $asset->save();

        return back()->with('success', 'Riwayat kerusakan berhasil dihapus.');
    }
}
