<?php

namespace App\Http\Controllers;

use App\Models\Hewan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HewanController extends Controller
{
    // Create (Tambah Hewan)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_hewan' => 'required|string|max:255',
            'jenis_hewan' => 'required|string|max:255',
            'spesies' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'umur' => 'required|integer|min:0',
            'foto_hewan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pasien_id' => 'required|exists:users,id',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_hewan')) {
            $fileName = $request->file('foto_hewan')->getClientOriginalName();
            $path = $request->file('foto_hewan')->storeAs('uploads/hewans', $fileName, 'public');
            $validatedData['foto_hewan'] = $path;
        }

        $hewan = Hewan::create($validatedData);

        return response()->json($hewan, 201);
    }

    // Read (Lihat semua hewan)
    public function index()
    {
        return Hewan::with('pasien')->get();
    }

    public function show($id)
    {
        try {
            // Mengambil data Hewan beserta data pasien terkait
            $hewan = Hewan::with('pasien')->find($id);

            // Jika Hewan tidak ditemukan, akan mengembalikan error 404
            if (!$hewan) {
                return response()->json([
                    'message' => 'Hewan tidak ditemukan'
                ], 404);
            }

            // Mengembalikan data hewan beserta pasien terkait
            return response()->json($hewan);

        } catch (\Exception $e) {
            // Menangani error jika ada masalah lain (misalnya query atau koneksi)
            return response()->json([
                'message' => 'Terjadi kesalahan, coba lagi nanti',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Update (Update data hewan)
    public function update(Request $request, $id)
    {
        $hewan = Hewan::findOrFail($id);

        $validatedData = $request->validate([
            'nama_hewan' => 'sometimes|required|string|max:255',
            'jenis_hewan' => 'sometimes|required|string|max:255',
            'spesies' => 'sometimes|required|string|max:255',
            'jenis_kelamin' => 'sometimes|required|in:Jantan,Betina',
            'umur' => 'sometimes|required|integer|min:0',
            'foto_hewan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pasien_id' => 'sometimes|required|exists:users,id',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_hewan')) {
            // Hapus file lama jika ada
            if ($hewan->foto_hewan) {
                Storage::disk('public')->delete($hewan->foto_hewan);
            }

            // Simpan foto baru
            $fileName = $request->file('foto_hewan')->getClientOriginalName();
            $path = $request->file('foto_hewan')->storeAs('uploads/hewans', $fileName, 'public');
            $validatedData['foto_hewan'] = $path;
        }

        $hewan->update($validatedData);

        return response()->json($hewan);
    }

    // Delete (Hapus hewan)
    public function destroy($id)
    {
        $hewan = Hewan::findOrFail($id);

        // Hapus file foto jika ada
        if ($hewan->foto_hewan) {
            Storage::disk('public')->delete($hewan->foto_hewan);
        }

        $hewan->delete();

        return response()->json(null, 204);
    }
}
