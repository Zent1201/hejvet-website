<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hewan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_hewan',
        'jenis_hewan',
        'jenis_kelamin',
        'umur',
        'foto_hewan',
        'spesies',
        'pasien_id',
    ];

    // Relasi ke User (Pasien)
    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }
}
