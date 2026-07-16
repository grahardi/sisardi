<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique(); // nomor urut, auto generate tapi bisa diedit
            $table->string('kode_umum'); // misal LPX untuk Laptop
            $table->string('kode_aset'); // unik dalam satu kode_umum
            $table->string('nama_barang');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->year('tahun_pembelian')->nullable();
            $table->foreignId('funding_source_id')->nullable()->constrained('funding_sources')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['baik', 'rusak', 'dalam_perbaikan'])->default('baik');
            $table->timestamps();

            $table->unique(['kode_umum', 'kode_aset']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
