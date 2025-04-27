<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'msg_text',
        'waktu_pesan_kirim',
        'waktu_pesan_selesai',
        'msg_type',
        'pasien_id',
        'dokter_id',

    ];

    // Relasi ke model User sebagai pasien
    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    // Relasi ke model User sebagai dokter
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    // Tentukan waktu_pesan_kirim secara otomatis
    protected static function booted()
    {
        static::creating(function ($message) {
            if (is_null($message->waktu_pesan_kirim)) {
                $message->waktu_pesan_kirim = now();
            }
        });
    }
}
