<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'username',
        'password',
        'nama',
        'role',
        'spesialisasi',
        'no_telp',
        'jadwal_dokter',
        'tgl_join',
        'foto_user',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
