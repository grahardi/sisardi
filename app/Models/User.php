<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    // Daftar fitur yang tersedia untuk diberikan ke petugas
    public static function availablePermissions(): array
    {
        return [
            'kategori'    => 'Kategori Aset',
            'aset'        => 'Manajemen Aset',
            'dana'        => 'Dana Pembelian',
            'lokasi'      => 'Lokasi/Tempat',
            'kerusakan'   => 'History Perbaikan',
            'peminjaman'  => 'Peminjaman',
            'peminjam'    => 'Data Guru/Siswa',
            'user'        => 'Manajemen User',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function hasPermission(string $key): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        return in_array($key, $this->permissions ?? []);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
