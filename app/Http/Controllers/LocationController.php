<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->paginate(15);
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        Location::create($data);

        return back()->with('success', 'Lokasi/tempat berhasil ditambahkan.');
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        $location->update($data);

        return back()->with('success', 'Lokasi/tempat berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        if ($location->assets()->exists()) {
            return back()->withErrors(['error' => 'Lokasi masih dipakai oleh data aset.']);
        }

        $location->delete();

        return back()->with('success', 'Lokasi/tempat berhasil dihapus.');
    }
}
