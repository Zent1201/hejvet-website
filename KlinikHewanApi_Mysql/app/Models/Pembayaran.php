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
        'tgl_bayar',
        'status_konsultasi',
        'status_service',
        'konsuls_id',
        'service_id',
        'pasien_id',
    ];
    // Relasi ke model Konsul (many-to-one relationship)
    public function konsul()
    {
        return $this->belongsTo(Konsul::class, 'konsuls_id');
    }

    // Relasi ke model Service (many-to-one relationship)
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Relasi ke model User (many-to-one relationship)
    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    // Relasi ke model KonfirmasiPembayaran
    public function konfirmasiPembayaran()
    {
        return $this->hasMany(KonfirmasiPembayaran::class, 'pembayaran_id');
    }
}
