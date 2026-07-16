<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'tanggal_kerusakan', 'tanggal_perbaikan',
        'tanggal_selesai_perbaikan', 'keterangan_kerusakan', 'status',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Tentukan status berdasarkan tanggal yang terisi, lalu sinkronkan ke status aset
    public function syncStatus(): void
    {
        if ($this->tanggal_selesai_perbaikan) {
            $this->status = 'selesai';
        } elseif ($this->tanggal_perbaikan) {
            $this->status = 'dalam_perbaikan';
        } else {
            $this->status = 'rusak';
        }
        $this->save();

        $asset = $this->asset;
        if ($this->status === 'selesai') {
            $asset->status = 'baik';
        } elseif ($this->status === 'dalam_perbaikan') {
            $asset->status = 'dalam_perbaikan';
        } else {
            $asset->status = 'rusak';
        }
        $asset->save();
    }
}
