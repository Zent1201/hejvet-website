<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'jenis_services',
        'harga',
        'image',
        'tgl_pemesanan',
        'deskripsi',

    ];

    // Relasi ke model Pembayaran (one-to-many relationship)
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'service_id');
    }
}
