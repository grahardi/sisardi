<?php

namespace App\Http\Controllers;

use App\Models\FundingSource;
use Illuminate\Http\Request;

class FundingSourceController extends Controller
{
    public function index()
    {
        $fundingSources = FundingSource::orderBy('name')->paginate(15);
        return view('funding_sources.index', compact('fundingSources'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        FundingSource::create($data);

        return back()->with('success', 'Dana pembelian berhasil ditambahkan.');
    }

    public function update(Request $request, FundingSource $fundingSource)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        $fundingSource->update($data);

        return back()->with('success', 'Dana pembelian berhasil diperbarui.');
    }

    public function destroy(FundingSource $fundingSource)
    {
        if ($fundingSource->assets()->exists()) {
            return back()->withErrors(['error' => 'Sumber dana masih dipakai oleh data aset.']);
        }

        $fundingSource->delete();

        return back()->with('success', 'Dana pembelian berhasil dihapus.');
    }
}
