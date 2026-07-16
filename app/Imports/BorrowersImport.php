<?php

namespace App\Imports;

use App\Models\Borrower;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Format kolom header Excel (baris pertama):
 * nama | nip_nis | kelas_jabatan | telp
 *
 * Tipe (guru/siswa) ditentukan dari halaman mana file ini diupload, bukan dari kolom Excel,
 * supaya template Guru dan Siswa bisa dipakai terpisah dan sederhana.
 */
class BorrowersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public int $imported = 0;
    public array $errors = [];

    public function __construct(private string $type)
    {
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $baris = $index + 2;

            $nama = trim((string) ($row['nama'] ?? ''));
            if ($nama === '') {
                $this->errors[] = "Baris {$baris}: kolom 'nama' wajib diisi.";
                continue;
            }

            $identityNumber = trim((string) ($row['nip_nis'] ?? ''));

            // Lewati jika NIP/NIS sudah ada (hindari duplikat saat re-upload)
            if ($identityNumber !== '' && Borrower::where('type', $this->type)->where('identity_number', $identityNumber)->exists()) {
                $this->errors[] = "Baris {$baris}: NIP/NIS '{$identityNumber}' sudah terdaftar, dilewati.";
                continue;
            }

            Borrower::create([
                'type' => $this->type,
                'name' => $nama,
                'identity_number' => $identityNumber ?: null,
                'unit' => $row['kelas_jabatan'] ?? null,
                'phone' => $row['telp'] ?? null,
            ]);

            $this->imported++;
        }
    }
}
