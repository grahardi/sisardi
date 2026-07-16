<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'asset_id', 'kondisi_pinjam', 'kondisi_kembali', 'is_returned', 'returned_at',
    ];

    protected $casts = [
        'is_returned' => 'boolean',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
