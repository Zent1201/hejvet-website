<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    // Menampilkan semua services (GET)
    public function index()
    {
        $services = Service::all();
        return response()->json($services, 200);
    }

    // Menampilkan service berdasarkan id (GET)
    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service tidak ditemukan'], 404);
        }

        return response()->json($service, 200);
    }

    // Membuat service baru (CREATE)
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'jenis_services' => 'required|string',
            'harga' => 'required|numeric',
            'image' => 'sometimes|image|mimes:jpg,png,jpeg|max:2048', // Validasi image
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Simpan image jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->storeAs('uploads', $image->getClientOriginalName(), 'public');
        }

        // Simpan service baru
        $service = Service::create([
            'jenis_services' => $request->jenis_services,
            'harga' => $request->harga,
            'image' => $imagePath,
            'tgl_pemesanan' => $request->tgl_pemesanan,
        ]);

        return response()->json([
            'message' => 'Service berhasil ditambahkan.',
            'data' => $service,
        ], 201);
    }

    // Mengupdate service berdasarkan id (PATCH untuk update satu-satu field)
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service tidak ditemukan'], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'jenis_services' => 'sometimes|string',
            'harga' => 'sometimes|numeric',
            'image' => 'sometimes|image|mimes:jpg,png,jpeg|max:2048', // Validasi image
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Update field satu-satu jika ada dalam request
        if ($request->has('jenis_services')) {
            $service->jenis_services = $request->jenis_services;
        }
        if ($request->has('harga')) {
            $service->harga = $request->harga;
        }
        if ($request->hasFile('image')) {
            // Hapus image lama jika ada
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            // Upload dan simpan image baru
            $image = $request->file('image');
            $imagePath = $image->storeAs('uploads', $image->getClientOriginalName(), 'public');
            $service->image = $imagePath;
        }

        // Simpan perubahan
        $service->save();

        return response()->json([
            'message' => 'Service berhasil diperbarui.',
            'data' => $service,
        ], 200);
    }

    // Menghapus service berdasarkan id (DELETE)
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service tidak ditemukan'], 404);
        }

        // Hapus image jika ada
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json(['message' => 'Service berhasil dihapus.'], 200);
    }
}
