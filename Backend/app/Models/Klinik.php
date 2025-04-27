<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klinik extends Model
{
    use HasFactory;

   protected $fillable = ['nama_klinik', 'foto_klinik', 'notelp_klinik', 'lokasi_klinik', 'jadwal_klinik'];

}
