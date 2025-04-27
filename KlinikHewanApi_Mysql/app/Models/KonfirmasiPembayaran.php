<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfirmasiPembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembayaran_id',
        'pasien_id',
        'nama_rekening',
        'tgl_bayar',
        'status_konsultasi',
        'status_service',
        'konsul_id',
        'service_id'
    ];

    // Relasi ke model Pembayaran
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

    // Relasi ke model User (Pasien)
    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }


    public function konsul()
    {
        return $this->belongsTo(Konsul::class,'konsul_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
}
