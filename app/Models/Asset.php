<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang', 'kode_umum', 'kode_aset', 'nama_barang',
        'category_id', 'location_id', 'tahun_pembelian',
        'funding_source_id', 'keterangan', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class);
    }

    public function repairHistories()
    {
        return $this->hasMany(RepairHistory::class)->orderByDesc('id');
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }

    // Item peminjaman yang masih aktif (belum dikembalikan)
    public function activeLoanItem()
    {
        return $this->hasOne(LoanItem::class)->where('is_returned', false);
    }

    public function isBorrowed(): bool
    {
        return $this->loanItems()->where('is_returned', false)->exists();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'baik' && ! $this->isBorrowed();
    }

    // Generate nomor kode_barang berikutnya (bisa diedit user setelahnya)
    public static function generateNextKodeBarang(): string
    {
        $last = static::withTrashed()
            ->selectRaw('MAX(CAST(kode_barang AS UNSIGNED)) as max_kode')
            ->value('max_kode');

        $next = ($last ?? 0) + 1;

        return str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
