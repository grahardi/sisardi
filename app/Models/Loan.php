<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code', 'borrower_id', 'user_id', 'tanggal_pinjam',
        'tanggal_kembali_rencana', 'tanggal_kembali_aktual', 'status', 'keterangan',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            if (empty($loan->transaction_code)) {
                $loan->transaction_code = 'PJM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(LoanItem::class);
    }

    public function refreshStatus(): void
    {
        $allReturned = $this->items()->where('is_returned', false)->doesntExist();
        $this->status = $allReturned ? 'dikembalikan' : 'dipinjam';
        if ($allReturned && ! $this->tanggal_kembali_aktual) {
            $this->tanggal_kembali_aktual = now()->toDateString();
        }
        $this->save();
    }
}
