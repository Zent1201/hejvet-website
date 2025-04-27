<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'nama',
        'jamkerja_dokter',
        'role',
        'spesialisasi',
        'no_telp',
        'jadwal_dokter',
        'foto_user',
        'email',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Menggunakan hashing Laravel
    ];

    // Relasi ke model Hewan sebagai pemilik hewan (pasien)
    public function hewans()
    {
        return $this->hasMany(Hewan::class, 'pasien_id');
    }

    // Relasi ke model Pembayaran (one-to-many relationship)
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'pasien_id');
    }

    // Jika user ini adalah seorang dokter, relasi ke konsultasi
    public function konsuls()
    {
        return $this->hasMany(Konsul::class, 'dokter_id');
    }

    // Relasi ke model Message sebagai pasien
    public function pasienMessages()
    {
        return $this->hasMany(Message::class, 'pasien_id');
    }

    // Relasi ke model Message sebagai dokter
    public function dokterMessages()
    {
        return $this->hasMany(Message::class, 'dokter_id');
    }
}
