<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsul extends Model
{
    protected $fillable = [
        'harga',
        'dokter_id',
    ];

    // Relasi ke tabel users sebagai dokter
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    // Relasi ke model Pembayaran (one-to-many relationship)
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'konsuls_id');
    }
}
