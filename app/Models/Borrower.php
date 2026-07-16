<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'identity_number', 'unit', 'phone'];

    public function loans()
    {
        return $this->hasMany(Loan::class)->orderByDesc('id');
    }
}
