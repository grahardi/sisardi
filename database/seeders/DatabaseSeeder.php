<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FundingSource;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============ USER ============
        User::firstOrCreate(
            ['email' => 'admin@smpn1turen.sch.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'permissions' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'petugas@smpn1turen.sch.id'],
            [
                'name' => 'Petugas Sarpras',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'permissions' => ['aset', 'kerusakan', 'peminjaman', 'peminjam'],
            ]
        );

        // ============ DANA PEMBELIAN ============
        foreach (['Bosda', 'Bos Pusat', 'Komite'] as $dana) {
            FundingSource::firstOrCreate(['name' => $dana]);
        }

        // ============ LOKASI ============
        foreach (['Ruang Kelas 7A', 'Ruang Kelas 8A', 'Ruang Kelas 9A', 'Ruang Guru', 'Laboratorium Komputer', 'Perpustakaan', 'Gudang'] as $lokasi) {
            Location::firstOrCreate(['name' => $lokasi]);
        }

        // ============ KATEGORI (contoh tree) ============
        $elektronik = Category::firstOrCreate(['name' => 'Elektronik', 'slug' => 'elektronik']);
        Category::firstOrCreate(['name' => 'Laptop', 'slug' => 'laptop', 'parent_id' => $elektronik->id]);
        Category::firstOrCreate(['name' => 'Proyektor/LCD', 'slug' => 'proyektor-lcd', 'parent_id' => $elektronik->id]);
        Category::firstOrCreate(['name' => 'Printer', 'slug' => 'printer', 'parent_id' => $elektronik->id]);

        $furnitur = Category::firstOrCreate(['name' => 'Furnitur', 'slug' => 'furnitur']);
        Category::firstOrCreate(['name' => 'Meja', 'slug' => 'meja', 'parent_id' => $furnitur->id]);
        Category::firstOrCreate(['name' => 'Kursi', 'slug' => 'kursi', 'parent_id' => $furnitur->id]);

        Category::firstOrCreate(['name' => 'Alat Olahraga', 'slug' => 'alat-olahraga']);
    }
}
