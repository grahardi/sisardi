<?php

namespace App\Http\Controllers;

use App\Imports\BorrowersImport;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BorrowerController extends Controller
{
    public function importForm()
    {
        return view('borrowers.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:guru,siswa',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new BorrowersImport($request->type);
        Excel::import($import, $request->file('file'));

        $label = $request->type === 'guru' ? 'guru' : 'siswa';
        $message = "{$import->imported} data {$label} berhasil diimport.";

        if (count($import->errors)) {
            return back()
                ->with('success', $message)
                ->withErrors($import->errors);
        }

        return redirect()->route('borrowers.index')->with('success', $message);
    }

    public function downloadTemplate(Request $request)
    {
        $type = $request->get('type', 'siswa');
        $csv = "nama,nip_nis,kelas_jabatan,telp\n";
        $csv .= $type === 'guru'
            ? "Contoh Nama Guru,19800101 200003 1 001,Guru Matematika,081234567890\n"
            : "Contoh Nama Siswa,1234567890,7A,081234567890\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"template_import_{$type}.csv\"",
        ]);
    }
    public function index(Request $request)
    {
        $query = Borrower::query();

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        $borrowers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('borrowers.index', compact('borrowers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:guru,siswa',
            'name' => 'required|string|max:150',
            'identity_number' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        Borrower::create($data);

        return back()->with('success', 'Data guru/siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Borrower $borrower)
    {
        $data = $request->validate([
            'type' => 'required|in:guru,siswa',
            'name' => 'required|string|max:150',
            'identity_number' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        $borrower->update($data);

        return back()->with('success', 'Data guru/siswa berhasil diperbarui.');
    }

    public function destroy(Borrower $borrower)
    {
        if ($borrower->loans()->exists()) {
            return back()->withErrors(['error' => 'Data ini memiliki riwayat peminjaman, tidak bisa dihapus.']);
        }

        $borrower->delete();

        return back()->with('success', 'Data guru/siswa berhasil dihapus.');
    }

    // Endpoint pencarian AJAX (dipakai di form peminjaman)
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $borrowers = Borrower::where('name', 'like', "%{$search}%")
            ->orWhere('identity_number', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'type', 'name', 'identity_number', 'unit']);

        return response()->json($borrowers);
    }
}
