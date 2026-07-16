# SiSardi - Sistem Sarpras Digital
SMP Negeri 1 Turen — Laravel 13 / PHP 8.3 / MySQL

Paket ini berisi **kode aplikasi** (migration, model, controller, route, view) yang
tinggal disalin ke dalam project Laravel 13 yang sudah kamu siapkan. Ini bukan
project Laravel yang berdiri sendiri — jadi tidak perlu `composer create-project` lagi.

## Changelog

- **Baru**: **Scan pakai kamera HP/laptop langsung dari browser** (library `html5-qrcode`) di
  halaman Peminjaman Cepat & Pengembalian Cepat — tidak perlu alat scanner fisik lagi.
- **Baru**: **Peminjaman & Pengembalian Cepat via Scan Kode Barang/QR**.
- **Baru**: **QR Code** per barang — bisa dicetak sebagai label dan dipakai untuk pencarian/peminjaman.
- **Baru**: **Foto Barang** pada Manajemen Aset (upload, tampil di daftar & detail, bisa dihapus/diganti).
- **Baru**: **Ikon Kategori** — pilih salah satu dari 16 ikon (Komputer, Meja, Kursi, Printer, dll) saat tambah/ubah kategori.
- Ganti tampilan ke **AdminLTE 4** (colorful theme).
- **Fix**: `Asset::generateNextKodeBarang()` sempat memanggil `withTrashed()` padahal
  tabel `assets` tidak pakai Soft Delete — menyebabkan `BadMethodCallException`. Sudah diperbaiki.
- **Baru**: fitur **Import Excel/CSV** untuk data Aset dan data Guru/Siswa (lihat bagian
  "Fitur Import Excel" di bawah). Butuh 1 package tambahan: `maatwebsite/excel`.

## 0. Install package tambahan

```bash
composer require maatwebsite/excel
```

### Wajib: buat symlink storage untuk foto barang

Foto barang disimpan di `storage/app/public/assets` dan diakses lewat `public/storage`.
Setelah migrate, jalankan sekali:

```bash
php artisan storage:link
```

Kalau lupa menjalankan ini, foto akan gagal tampil (404) walau berhasil ter-upload.

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
Nama Barang, Kategori, Tempat, Tahun Pembelian, Dana Pembelian, **Foto Barang**, Keterangan.
Status aset (`baik` / `rusak` / `dalam_perbaikan`) di-update otomatis dari menu
History Perbaikan, bukan diedit manual.

Foto barang: opsional, format JPG/PNG/WEBP maksimal 2MB, tampil sebagai thumbnail
di daftar aset dan foto besar di halaman detail. Saat mengubah data, ada opsi
"Hapus foto ini" untuk menghapus tanpa mengganti dengan foto baru.

### 2b. Ikon Kategori
Saat tambah/ubah kategori, ada galeri 16 pilihan ikon (Komputer/Laptop, PC, Printer,
Proyektor, Meja, Kursi, Lemari/Rak, Buku, Alat Olahraga, Alat Musik, Alat Lab,
Alat Kebersihan, Elektronik/Listrik, Kendaraan, Bangunan, Umum/Lainnya) — dipakai
memakai Bootstrap Icons + Font Awesome Free (keduanya sudah dimuat lewat CDN di layout).
Ikon terpilih ditampilkan di depan nama kategori pada tampilan tree.

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

### 6. Import Excel/CSV

Tersedia di menu **Manajemen Aset** ("Import Excel") dan **Data Guru/Siswa** ("Import Excel"):

- **Import Aset**: kolom `kode_barang, kode_umum, kode_aset, nama_barang, kategori, tempat,
  tahun_pembelian, dana_pembelian, keterangan`. `kode_barang` boleh dikosongkan (auto-generate).
  Nama kategori/tempat/dana yang belum ada di master data akan dibuatkan otomatis. Baris dengan
  kombinasi kode_umum + kode_aset yang sudah dipakai akan dilewati.
- **Import Guru/Siswa**: kolom `nama, nip_nis, kelas_jabatan, telp`. Pilih tipe (Guru/Siswa)
  sebelum upload — file yang sama formatnya dipakai untuk keduanya, hanya beda tujuan tabelnya.
  NIP/NIS yang sudah terdaftar untuk tipe yang sama akan dilewati (anti-dobel saat re-upload).

Kedua halaman menyediakan tombol "Unduh Template" (CSV) supaya format kolomnya pasti sesuai.
Hasil import menampilkan jumlah baris berhasil, dan daftar baris yang dilewati/gagal beserta alasannya.

### 7. Manajemen User & Hak Akses
Dua role: **Super Admin** (akses semua fitur, tidak bisa dibatasi) dan
**Petugas** (hak aksesnya dipilih per-fitur saat membuat/mengubah user: Kategori,
Aset, Dana Pembelian, Lokasi, Kerusakan, Peminjaman, Data Guru/Siswa, dan
Manajemen User). Middleware `permission:<fitur>` di `routes/web.php` yang
menegakkan pembatasan ini di level route.

---

### 8. Peminjaman & Pengembalian Cepat (Scan Kode Barang/QR)

Dua cara scan didukung, keduanya tanpa install apa pun di HP/perangkat:

- **Scanner fisik** (barcode/QR gun USB atau Bluetooth): bekerja seperti keyboard (mengetik lalu
  Enter), otomatis kompatibel dengan kotak input scan di halaman-halaman berikut.
- **Kamera HP/laptop langsung dari browser**: tombol **"Scan Pakai Kamera HP"** memakai library
  `html5-qrcode` (client-side, lewat CDN) untuk membuka kamera perangkat dan membaca QR code
  secara langsung — tidak perlu aplikasi tambahan, cukup browser modern (Chrome/Safari/Firefox).

  > ⚠️ **Penting**: fitur kamera browser (`getUserMedia`) hanya diizinkan browser di halaman
  > **HTTPS**, atau di `localhost` saat development. Kalau server produksi masih pakai HTTP biasa,
  > tombol kamera akan gagal minta izin — scanner fisik & input manual tetap berfungsi normal
  > di HTTP, hanya opsi kamera browser yang butuh HTTPS.

Halaman yang tersedia:
- **Peminjaman cepat**: di halaman keranjang peminjaman ("Buat Peminjaman Baru"), ada kotak
  "Scan Kode Barang / QR Code" + tombol kamera, terpisah dari pencarian manual. Setiap scan
  langsung menambahkan barang itu ke keranjang (kalau tersedia).
- **Pengembalian cepat**: menu Peminjaman → **"Pengembalian Cepat (Scan)"**. Halaman khusus untuk
  sesi scan berturut-turut (scanner fisik maupun kamera) — setiap kode yang discan langsung dicek
  statusnya; kalau sedang dipinjam, langsung ditandai dikembalikan saat itu juga, tercatat di panel
  "Riwayat Scan Sesi Ini" tanpa reload halaman. Mode kamera otomatis mencegah barang yang sama
  terbaca berulang dalam 3 detik (supaya tidak double-scan saat kamera masih mengarah ke QR yang sama).

### 9. QR Code Barang

Setiap barang punya QR code yang berisi Kode Barang-nya (di-generate langsung di browser
pakai library `qrcodejs`, tidak perlu request ke server pihak ketiga):

- Di halaman **detail aset**: ada kartu "Label QR Barang" lengkap dengan tombol "Cetak Label"
  (memakai CSS print khusus supaya yang tercetak cuma label QR-nya saja).
- Di halaman **daftar aset**: tombol ikon QR di kolom Aksi tiap baris, membuka modal QR + tombol cetak.
- Di halaman **keranjang peminjaman**, hasil pencarian barang juga punya tombol ikon QR
  untuk melihat/mencetak ulang label kalau perlu.

Alur kerja yang disarankan: cetak label QR dari halaman detail/daftar aset, tempel di barang
fisik, lalu gunakan scanner untuk peminjaman cepat maupun pengembalian cepat.

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
