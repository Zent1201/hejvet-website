<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    // Menampilkan semua pembayaran
    public function index()
    {
        $pembayarans = Pembayaran::all();
        return response()->json($pembayarans, 200);
    }

    // Menampilkan pembayaran berdasarkan id
    public function show($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json($pembayaran, 200);
    }

    // Membuat pembayaran baru (CREATE)
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'metode' => 'required|string|max:255',
            'norek' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Upload file gambar jika ada
        if ($request->hasFile('image')) {
            $fileName = $request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads/pembayarans', $fileName, 'public');
            $validatedData['image'] = $filePath;
        }

        // Simpan data pembayaran baru
        $pembayaran = Pembayaran::create($validatedData);

        return response()->json([
            'message' => 'Pembayaran berhasil dibuat.',
            'data' => $pembayaran
        ], 201);
    }

    // Mengupdate pembayaran berdasarkan id (UPDATE)
    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        // Validasi input
        $validatedData = $request->validate([
            'metode' => 'sometimes|required|string|max:255',
            'norek' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update metode pembayaran jika ada
        if ($request->has('metode')) {
            $pembayaran->metode = $request->metode;
        }

        // Update norek jika ada
        if ($request->has('norek')) {
            $pembayaran->norek = $request->norek;
        }

        // Update file gambar jika ada
        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($pembayaran->image) {
                Storage::disk('public')->delete($pembayaran->image);
            }
            // Upload dan simpan file baru
            $fileName = $request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads/pembayarans', $fileName, 'public');
            $pembayaran->image = $filePath;
        }

        // Simpan perubahan
        $pembayaran->save();

        return response()->json([
            'message' => 'Pembayaran berhasil diperbarui.',
            'data' => $pembayaran
        ], 200);
    }

    // Menghapus pembayaran berdasarkan id (DELETE)
    public function destroy($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        // Hapus file jika ada
        if ($pembayaran->image) {
            Storage::disk('public')->delete($pembayaran->image);
        }

        $pembayaran->delete();

        return response()->json(['message' => 'Pembayaran berhasil dihapus.'], 200);
    }
}
