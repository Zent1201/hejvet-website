<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'metode',
        'norek',
        'image',
        'total_bayar',
    ];
    // Relasi dengan tabel konfirmasi_pembayarans
    public function konfirmasiPembayaran()
    {
        return $this->hasOne(KonfirmasiPembayaran::class, 'pembayaran_id');
    }

    // Relasi dengan tabel status_pembayarans
    public function statusPembayaran()
    {
        return $this->hasOne(StatusPembayaran::class, 'pembayaran_id');
    }

}
