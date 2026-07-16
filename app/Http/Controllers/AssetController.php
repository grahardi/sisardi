<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use App\Models\Asset;
use App\Models\Category;
use App\Models\FundingSource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    public function importForm()
    {
        return view('assets.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new AssetsImport();
        Excel::import($import, $request->file('file'));

        $message = "{$import->imported} data aset berhasil diimport.";
        if (count($import->errors)) {
            return back()
                ->with('success', $message)
                ->withErrors($import->errors);
        }

        return redirect()->route('assets.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $csv = "kode_barang,kode_umum,kode_aset,nama_barang,kategori,tempat,tahun_pembelian,dana_pembelian,keterangan\n";
        $csv .= ",LPX,001,Laptop Asus X441,Laptop,Laboratorium Komputer,2023,Bosda,Contoh baris; kode_barang boleh dikosongkan\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_aset.csv"',
        ]);
    }
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'fundingSource']);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('kode_umum', 'like', "%{$search}%")
                  ->orWhere('kode_aset', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $assets = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $fundingSources = FundingSource::orderBy('name')->get();
        $nextKode = Asset::generateNextKodeBarang();

        return view('assets.create', compact('categories', 'locations', 'fundingSources', 'nextKode'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('assets', 'public');
        }

        Asset::create($data);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $fundingSources = FundingSource::orderBy('name')->get();

        return view('assets.edit', compact('asset', 'categories', 'locations', 'fundingSources'));
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $this->validateData($request, $asset);

        if ($request->boolean('hapus_foto') && $asset->foto) {
            Storage::disk('public')->delete($asset->foto);
            $data['foto'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($asset->foto) {
                Storage::disk('public')->delete($asset->foto);
            }
            $data['foto'] = $request->file('foto')->store('assets', 'public');
        }

        $asset->update($data);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->isBorrowed()) {
            return back()->withErrors(['error' => 'Aset sedang dipinjam, tidak bisa dihapus.']);
        }

        if ($asset->foto) {
            Storage::disk('public')->delete($asset->foto);
        }

        $asset->delete();

        return back()->with('success', 'Aset berhasil dihapus.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'location', 'fundingSource', 'repairHistories', 'loanItems.loan.borrower']);
        return view('assets.show', compact('asset'));
    }

    private function validateData(Request $request, ?Asset $asset = null): array
    {
        $assetId = $asset?->id;

        $data = $request->validate([
            'kode_barang' => [
                'required', 'string', 'max:50',
                Rule::unique('assets', 'kode_barang')->ignore($assetId),
            ],
            'kode_umum' => 'required|string|max:50',
            'kode_aset' => [
                'required', 'string', 'max:50',
                Rule::unique('assets')->where(fn($q) => $q->where('kode_umum', $request->kode_umum))->ignore($assetId),
            ],
            'nama_barang' => 'required|string|max:200',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'tahun_pembelian' => 'nullable|digits:4|integer|min:1990|max:' . (date('Y') + 1),
            'funding_source_id' => 'nullable|exists:funding_sources,id',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'kode_aset.unique' => 'Kode Aset ini sudah dipakai pada Kode Umum yang sama.',
            'kode_barang.unique' => 'Kode Barang sudah digunakan aset lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // 'foto' hanya dipakai untuk validasi upload; path finalnya diisi terpisah di store()/update()
        unset($data['foto']);

        return $data;
    }
}
