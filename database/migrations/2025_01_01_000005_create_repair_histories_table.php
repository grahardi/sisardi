<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->date('tanggal_kerusakan')->nullable();
            $table->date('tanggal_perbaikan')->nullable(); // mulai perbaikan
            $table->date('tanggal_selesai_perbaikan')->nullable();
            $table->text('keterangan_kerusakan')->nullable();
            $table->enum('status', ['rusak', 'dalam_perbaikan', 'selesai'])->default('rusak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_histories');
    }
};
