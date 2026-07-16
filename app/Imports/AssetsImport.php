<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\FundingSource;
use App\Models\Location;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Format kolom header Excel (baris pertama), tidak case sensitive, spasi jadi underscore:
 * kode_barang | kode_umum | kode_aset | nama_barang | kategori | tempat | tahun_pembelian | dana_pembelian | keterangan
 *
 * - kode_barang boleh dikosongkan -> akan di-generate otomatis.
 * - kategori / tempat / dana_pembelian diisi dengan NAMA (bukan id). Kalau nama belum ada di
 *   master data, akan dibuatkan otomatis (firstOrCreate) supaya import tidak gagal karena lupa
 *   menambahkan master data dulu.
 */
class AssetsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public int $imported = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $baris = $index + 2; // +2 karena heading row + index mulai dari 0

            $namaBarang = trim((string) ($row['nama_barang'] ?? ''));
            $kodeUmum = trim((string) ($row['kode_umum'] ?? ''));
            $kodeAset = trim((string) ($row['kode_aset'] ?? ''));

            if ($namaBarang === '' || $kodeUmum === '' || $kodeAset === '') {
                $this->errors[] = "Baris {$baris}: nama_barang, kode_umum, dan kode_aset wajib diisi.";
                continue;
            }

            // Cegah duplikasi kode_aset dalam kode_umum yang sama
            if (Asset::where('kode_umum', $kodeUmum)->where('kode_aset', $kodeAset)->exists()) {
                $this->errors[] = "Baris {$baris}: Kode Aset '{$kodeAset}' sudah dipakai pada Kode Umum '{$kodeUmum}'.";
                continue;
            }

            $kodeBarang = trim((string) ($row['kode_barang'] ?? ''));
            if ($kodeBarang === '') {
                $kodeBarang = Asset::generateNextKodeBarang();
            } elseif (Asset::where('kode_barang', $kodeBarang)->exists()) {
                $this->errors[] = "Baris {$baris}: Kode Barang '{$kodeBarang}' sudah digunakan aset lain.";
                continue;
            }

            $categoryId = null;
            if (! empty($row['kategori'])) {
                $categoryId = Category::firstOrCreate(['name' => trim($row['kategori'])])->id;
            }

            $locationId = null;
            if (! empty($row['tempat'])) {
                $locationId = Location::firstOrCreate(['name' => trim($row['tempat'])])->id;
            }

            $fundingSourceId = null;
            if (! empty($row['dana_pembelian'])) {
                $fundingSourceId = FundingSource::firstOrCreate(['name' => trim($row['dana_pembelian'])])->id;
            }

            Asset::create([
                'kode_barang' => $kodeBarang,
                'kode_umum' => $kodeUmum,
                'kode_aset' => $kodeAset,
                'nama_barang' => $namaBarang,
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'tahun_pembelian' => $row['tahun_pembelian'] ?? null,
                'funding_source_id' => $fundingSourceId,
                'keterangan' => $row['keterangan'] ?? null,
                'status' => 'baik',
            ]);

            $this->imported++;
        }
    }
}
