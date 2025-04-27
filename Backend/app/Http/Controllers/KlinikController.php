<?php

namespace App\Http\Controllers;

use App\Models\Klinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class KlinikController extends Controller
{
    // Get All Kliniks
    public function index()
    {
        $kliniks = Klinik::all();
        return response()->json($kliniks);
    }

    // Get Klinik By ID
    public function show($id)
    {
        $klinik = Klinik::findOrFail($id);
        return response()->json($klinik);
    }

  // Create a new klinik
  public function store(Request $request)
  {
      $validated = $request->validate([
          'nama_klinik' => 'required|string|max:255',
          'foto_klinik' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // File image
          'notelp_klinik' => 'required|string|max:20',
          'lokasi_klinik' => 'required|string|max:255',
          'jadwal_klinik' => 'nullable|string|max:255',
      ]);

      // Handle file upload
      if ($request->hasFile('foto_klinik')) {
          $fileName = time() . '_' . $request->file('foto_klinik')->getClientOriginalName();
          $path = $request->file('foto_klinik')->storeAs('uploads/klinik', $fileName, 'public');
          $validated['foto_klinik'] = $path; // Save the file path to the database
      }

      $klinik = Klinik::create($validated);

      return response()->json($klinik, 201);
  }


        //Update
        public function update(Request $request, $id)
        {
            // Logging untuk debugging
            Log::info('Full Request Data:', $request->all()); // Log semua data request
            Log::info('Files:', $request->file()); // Log semua file yang diupload

            // Cari klinik berdasarkan ID
            $klinik = Klinik::findOrFail($id);

            // Logging data klinik sebelum update
            Log::info('Data Klinik sebelum update:', $klinik->toArray());

            // Validasi input
            $validated = $request->validate([
                'nama_klinik' => 'sometimes|required|string|max:255',
                'foto_klinik' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi file gambar
                'notelp_klinik' => 'sometimes|required|string|max:20',
                'lokasi_klinik' => 'sometimes|required|string|max:255',
                'jadwal_klinik' => 'nullable|string|max:255',
            ]);

            // Logging setelah validasi
            Log::info('Validated data:', $validated);

            // Handle file upload jika ada file baru
            if ($request->hasFile('foto_klinik')) {
                Log::info('File foto klinik ditemukan, siap untuk upload.');

                // Hapus file lama jika ada
                if ($klinik->foto_klinik) {
                    $oldFilePath = 'public/' . $klinik->foto_klinik;
                    if (Storage::exists($oldFilePath)) {
                        Storage::delete($oldFilePath);
                        Log::info('File foto klinik lama dihapus:', ['path' => $oldFilePath]);
                    }
                }

                // Simpan file baru
                $fileName = time() . '_' . $request->file('foto_klinik')->getClientOriginalName();
                $path = $request->file('foto_klinik')->storeAs('uploads/klinik', $fileName, 'public');
                $validated['foto_klinik'] = $path; // Simpan path file baru ke database

                Log::info('File foto klinik baru disimpan:', ['filename' => $fileName]);
            } else {
                Log::info('Tidak ada file foto klinik yang diupload.');
            }

            // Update data klinik dengan data yang sudah divalidasi
            $klinik->update($validated);

            // Logging data klinik setelah update
            Log::info('Data Klinik setelah update:', $klinik->toArray());

            // Return response dengan data klinik yang terupdate
            return response()->json($klinik, 200);
        }

         // Delete Klinik
         public function destroy($id)
         {
             $klinik = Klinik::findOrFail($id);

             // Hapus foto klinik jika ada
             if ($klinik->foto_klinik) {
                 // Periksa apakah file ada di storage
                 $fotoPath = 'foto_klinik/' . basename($klinik->foto_klinik);  // Path foto dalam folder 'public/foto_klinik'

                 if (Storage::disk('public')->exists($fotoPath)) {
                     Storage::disk('public')->delete($fotoPath);
                 } else {
                     return response()->json(['error' => 'File foto tidak ditemukan di server'], 404);
                 }
             }

             // Hapus record dari database
             $klinik->delete();

             return response()->json(['message' => 'Klinik berhasil dihapus'], 200);
         }




        // Helper to handle image upload
        private function storeImage($image)
        {
            // Simpan file dan return path yang tersimpan di storage public
            $path = $image->store('foto_klinik', 'public');  // Path relatif: 'foto_klinik/nama_file.jpg'

            // Log path file yang disimpan untuk debugging
            Log::info("File disimpan di path: " . $path);

            return $path;
        }



}
