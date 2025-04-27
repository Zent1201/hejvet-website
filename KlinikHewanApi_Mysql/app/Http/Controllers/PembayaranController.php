<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    // Menampilkan semua pembayaran (GET)
    public function index()
    {
        $pembayarans = Pembayaran::all(); // Mengambil semua pembayaran
        return response()->json($pembayarans, 200);
    }

    // Menampilkan pembayaran berdasarkan id (GET)
    public function show($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json($pembayaran, 200);
    }



    // Menghapus pembayaran berdasarkan id (DELETE)
    public function destroy($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        $pembayaran->delete();

        return response()->json(['message' => 'Pembayaran berhasil dihapus.'], 200);
    }
}
