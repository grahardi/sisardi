# SiSardi - Sistem Sarpras Digital
SMP Negeri 1 Turen — Laravel 13 / PHP 8.3 / MySQL

Paket ini berisi **kode aplikasi** (migration, model, controller, route, view) yang
tinggal disalin ke dalam project Laravel 13 yang sudah kamu siapkan. Ini bukan
project Laravel yang berdiri sendiri — jadi tidak perlu `composer create-project` lagi.

## 1. Salin folder ke project Laravel kamu

Salin isi folder ini ke project Laravel kamu (folder tujuan di dalam tanda kurung):

```
database/migrations/*.php     -> database/migrations/
database/seeders/DatabaseSeeder.php -> database/seeders/DatabaseSeeder.php (timpa/replace)
app/Models/*.php               -> app/Models/ (timpa/replace User.php)
app/Http/Controllers/*.php     -> app/Http/Controllers/
app/Http/Middleware/CheckPermission.php -> app/Http/Middleware/
routes/web.php                 -> routes/web.php (timpa/replace)
resources/views/*               -> resources/views/
```

## 2. Daftarkan middleware `permission`

Laravel 13 mendaftarkan middleware alias di `bootstrap/app.php`. Buka file itu dan
tambahkan alias berikut di dalam `->withMiddleware()`:

```php
->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
    $middleware->alias([
        'permission' => \App\Http\Middleware\CheckPermission::class,
    ]);
})
```

## 3. Set koneksi database

Sesuaikan `.env` kamu:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sisardi
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `sisardi` di MySQL.

## 4. Migrasi & Seed

```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat:
- **Super Admin**: `admin@smpn1turen.sch.id` / `password123` (akses semua fitur)
- **Petugas**: `petugas@smpn1turen.sch.id` / `password123` (akses: Aset, Kerusakan, Peminjaman, Data Guru/Siswa)
- Data awal: Dana Pembelian (Bosda, Bos Pusat, Komite), beberapa Lokasi, dan contoh Kategori bertingkat (Elektronik > Laptop/Proyektor/Printer, Furnitur > Meja/Kursi, dst).

**Segera ganti password default setelah login pertama kali**, lewat menu Manajemen User.

## 5. Jalankan

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` lalu login.

---

## Ringkasan Fitur

### 1. Kategori Aset
Struktur pohon (tree) tak terbatas seperti kategori di WordPress/Joomla — bisa
tambah kategori utama, tambah sub-kategori di dalam kategori manapun, ubah nama/
pindah induk, dan hapus (kategori yang masih punya sub-kategori atau dipakai aset
tidak bisa dihapus).

### 2. Manajemen Aset
Field: Kode Barang (nomor urut auto-generate, tapi bisa diedit & harus unik),
Kode Umum (mis. `LPX` untuk semua laptop), Kode Aset (unik **di dalam** Kode Umum
yang sama — jadi dua barang beda Kode Umum boleh punya Kode Aset yang sama),
Nama Barang, Kategori, Tempat, Tahun Pembelian, Dana Pembelian, Keterangan.
Status aset (`baik` / `rusak` / `dalam_perbaikan`) di-update otomatis dari menu
History Perbaikan, bukan diedit manual.

### 3. Dana Pembelian
CRUD sederhana (tambah/ubah/hapus) — Bosda, Bos Pusat, Komite, dan bisa tambah
sumber dana lain kapan saja.

### 4. History Perbaikan
Input: Barang, Tanggal Kerusakan, Tanggal Perbaikan, Tanggal Selesai Perbaikan,
Keterangan Kerusakan. Status ditentukan otomatis:
- Hanya Tanggal Kerusakan diisi → status **Rusak**, aset ikut jadi Rusak.
- Tanggal Perbaikan diisi → status **Dalam Perbaikan**, aset ikut jadi Dalam Perbaikan.
- Tanggal Selesai diisi → status **Selesai**, aset kembali jadi Baik.

### 5. Peminjaman (fitur utama)
Data peminjam ada 2 jenis: **Guru** dan **Siswa** (satu tabel `borrowers` dengan
kolom `type`). Ada dua alur masuk ke halaman keranjang peminjaman yang sama:

- **Alur A — mulai dari barang**: buka menu Peminjaman → Buat Peminjaman Baru →
  cari barang (hanya barang berstatus Baik & tidak sedang dipinjam yang muncul)
  → tambah ke keranjang → baru pilih Guru/Siswa → checkout.
- **Alur B — mulai dari peminjam**: buka menu Data Guru/Siswa → cari orangnya →
  klik tombol "Pinjamkan barang ke orang ini" → otomatis masuk ke keranjang
  dengan peminjam sudah terpilih → tinggal cari & tambah barang → checkout.

Checkout bisa kolektif (banyak barang sekaligus, seperti belanja lalu checkout).
Setelah checkout, transaksi mendapat kode unik (`PJM-YYYYMMDD-XXXX`). Pengembalian
bisa per-barang atau sekaligus semua barang dalam satu transaksi; begitu semua
barang di satu transaksi kembali, status transaksi otomatis jadi "dikembalikan".

### 6. Manajemen User & Hak Akses
Dua role: **Super Admin** (akses semua fitur, tidak bisa dibatasi) dan
**Petugas** (hak aksesnya dipilih per-fitur saat membuat/mengubah user: Kategori,
Aset, Dana Pembelian, Lokasi, Kerusakan, Peminjaman, Data Guru/Siswa, dan
Manajemen User). Middleware `permission:<fitur>` di `routes/web.php` yang
menegakkan pembatasan ini di level route.

---

## Catatan Teknis

- Tidak ada dependency composer tambahan di luar Laravel default (`illuminate/auth`
  bawaan framework) — jadi cukup `composer install` seperti biasa dari project
  kamu, tidak perlu paket permission pihak ketiga.
- Login memakai `Auth::attempt` bawaan Laravel, bukan Breeze/Jetstream, supaya
  ringan dan mudah dikustomisasi.
- UI pakai Bootstrap 5 + Bootstrap Icons lewat CDN (butuh koneksi internet saat
  dibuka; kalau mau offline, ganti ke asset lokal via Vite).
- Pencarian barang & peminjam di halaman keranjang peminjaman pakai AJAX
  (`fetch`) ke endpoint `loans.search_assets` dan `borrowers.search`.
- Silakan sesuaikan lagi field/istilah (mis. kelas, mapel) sesuai kebutuhan riil
  sekolah — struktur tabel sudah generik supaya gampang dikembangkan.
