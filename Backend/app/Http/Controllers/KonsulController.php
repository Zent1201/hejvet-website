<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Konsul;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KonsulController extends Controller
{
    // Menampilkan semua konsultasi (GET)
    public function index()
    {
        $konsuls = Konsul::with('dokter')->get(); // Mengambil semua data konsultasi dengan relasi dokter
        return response()->json($konsuls, 200);
    }

    // Menampilkan konsultasi berdasarkan id (GET)
    public function show($id)
    {
        $konsul = Konsul::with('dokter')->find($id);

        if (!$konsul) {
            return response()->json(['message' => 'Konsultasi tidak ditemukan'], 404);
        }

        return response()->json($konsul, 200);
    }

    // Membuat konsultasi baru (CREATE)
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'harga' => 'required|numeric',
            'dokter_id' => 'required|exists:users,id', // Memastikan dokter_id valid dan ada di users
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Cari dokter berdasarkan ID yang diinputkan
        $dokter = User::where('id', $request->dokter_id)->where('role', 'dokter')->first(); // Pastikan dokter memiliki role 'dokter'

        if (!$dokter) {
            return response()->json(['message' => 'Dokter tidak ditemukan atau bukan seorang dokter'], 404);
        }

        // Simpan konsultasi baru
        $konsul = Konsul::create([
            'harga' => $request->harga,
            'dokter_id' => $dokter->id, // Set dokter_id berdasarkan dokter yang dipilih
        ]);

        return response()->json([
            'message' => 'Konsultasi berhasil dibuat.',
            'data' => $konsul,
        ], 201);
    }

    // Mengupdate konsultasi berdasarkan id (UPDATE)
    public function update(Request $request, $id)
    {
        $konsul = Konsul::find($id);

        if (!$konsul) {
            return response()->json(['message' => 'Konsultasi tidak ditemukan'], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'harga' => 'required|numeric',
            'dokter_id' => 'required|exists:users,id', // Validasi dokter_id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Cari dokter berdasarkan ID
        $dokter = User::where('id', $request->dokter_id)->where('role', 'dokter')->first();

        if (!$dokter) {
            return response()->json(['message' => 'Dokter tidak ditemukan atau bukan seorang dokter'], 404);
        }

        // Update data konsultasi
        $konsul->update([
            'harga' => $request->harga,
            'dokter_id' => $dokter->id,
        ]);

        return response()->json([
            'message' => 'Konsultasi berhasil diperbarui.',
            'data' => $konsul,
        ], 200);
    }

    // Menghapus konsultasi berdasarkan id (DELETE)
    public function destroy($id)
    {
        $konsul = Konsul::find($id);

        if (!$konsul) {
            return response()->json(['message' => 'Konsultasi tidak ditemukan'], 404);
        }

        $konsul->delete();

        return response()->json(['message' => 'Konsultasi berhasil dihapus.'], 200);
    }
}
