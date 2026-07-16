<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id', 'order', 'icon'];

    // Daftar pilihan ikon untuk kategori (dipakai di form tambah/ubah kategori)
    public static function iconOptions(): array
    {
        return [
            'bi bi-laptop' => 'Komputer/Laptop',
            'bi bi-pc-display' => 'PC/Desktop',
            'bi bi-printer' => 'Printer',
            'bi bi-projector' => 'Proyektor/LCD',
            'fa-solid fa-table' => 'Meja',
            'fa-solid fa-chair' => 'Kursi',
            'fa-solid fa-box-archive' => 'Lemari/Rak',
            'bi bi-book' => 'Buku/Perpustakaan',
            'fa-solid fa-dumbbell' => 'Alat Olahraga',
            'fa-solid fa-music' => 'Alat Musik',
            'fa-solid fa-flask' => 'Alat Lab/Sains',
            'fa-solid fa-broom' => 'Alat Kebersihan',
            'bi bi-lightning-charge' => 'Elektronik/Listrik',
            'fa-solid fa-car' => 'Kendaraan',
            'bi bi-building' => 'Bangunan/Gedung',
            'bi bi-folder2' => 'Umum/Lainnya',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });
    }

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    // Ambil semua kategori dalam bentuk tree (root only, children dimuat rekursif)
    public static function tree()
    {
        return static::whereNull('parent_id')->orderBy('order')->with('childrenRecursive')->get();
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
