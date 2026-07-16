<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
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
