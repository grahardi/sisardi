<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $tree = Category::tree();
        $allCategories = Category::orderBy('name')->get(); // untuk dropdown pilih induk
        return view('categories.index', compact('tree', 'allCategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($data['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'Kategori tidak boleh menjadi induk dirinya sendiri.']);
        }

        $category->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->withErrors(['error' => 'Hapus dulu sub-kategori di dalamnya.']);
        }
        if ($category->assets()->exists()) {
            return back()->withErrors(['error' => 'Kategori masih dipakai oleh data aset.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
