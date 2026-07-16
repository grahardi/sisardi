<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['guru', 'siswa']);
            $table->string('name');
            $table->string('identity_number')->nullable(); // NIP / NIS
            $table->string('unit')->nullable(); // kelas (siswa) / mapel-jabatan (guru)
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
