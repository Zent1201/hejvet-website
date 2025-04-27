<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Menampilkan semua pesan (GET)
    public function index()
    {
        $messages = Message::all();
        return response()->json($messages, 200);
    }

    // Menampilkan pesan berdasarkan id (GET)
    public function show($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        return response()->json($message, 200);
    }

    // Membuat pesan baru (CREATE)
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'msg_text' => 'sometimes|string',
            'msg_type' => 'nullable|file|mimes:pdf,xlsx,jpeg,png,jpg|max:2048', // Validasi file
            'pasien_id' => 'required|exists:users,id',
            'dokter_id' => 'required|exists:users,id',
        ], [
            'msg_text.string' => 'Pesan teks harus berupa string.',
            'msg_type.mimes' => 'File harus berupa salah satu tipe berikut: pdf, xlsx, jpeg, png, jpg.',
            'msg_type.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            'pasien_id.required' => 'ID pasien wajib diisi.',
            'dokter_id.required' => 'ID dokter wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Simpan file jika ada file yang di-upload
        $filePath = null;
        if ($request->hasFile('msg_type')) {
            $file = $request->file('msg_type');
            $filePath = $file->storeAs('uploads/messages', $file->getClientOriginalName(), 'public');
        }

        // Simpan pesan baru
        $message = Message::create([
            'msg_text' => $request->msg_text, // Pesan teks
            'msg_type' => $filePath, // Pesan berupa file (pdf, excel, image, etc.)
            'pasien_id' => $request->pasien_id,
            'dokter_id' => $request->dokter_id,
            'waktu_pesan_kirim' => now(),
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim.',
            'data' => $message,
        ], 201);
    }

    // Mengupdate pesan berdasarkan id (UPDATE)
    public function update(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'msg_text' => 'sometimes|string',
            'msg_type' => 'nullable|file|mimes:pdf,xlsx,jpeg,png,jpg|max:2048', // Validasi file
        ], [
            'msg_text.string' => 'Pesan teks harus berupa string.',
            'msg_type.mimes' => 'File harus berupa salah satu tipe berikut: pdf, xlsx, jpeg, png, jpg.',
            'msg_type.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Update msg_text jika ada
        if ($request->has('msg_text')) {
            $message->msg_text = $request->msg_text;
        }

        // Update file jika ada
        if ($request->hasFile('msg_type')) {
            // Hapus file lama jika ada
            if ($message->msg_type) {
                Storage::disk('public')->delete($message->msg_type);
            }
            // Upload dan simpan file baru
            $file = $request->file('msg_type');
            $filePath = $file->storeAs('uploads/messages', $file->getClientOriginalName(), 'public');
            $message->msg_type = $filePath;
        }

        // Simpan perubahan
        $message->save();

        return response()->json([
            'message' => 'Pesan berhasil diperbarui.',
            'data' => $message,
        ], 200);
    }

    // Menghapus pesan berdasarkan id (DELETE)
    public function destroy($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        // Hapus file jika ada
        if ($message->msg_type) {
            Storage::disk('public')->delete($message->msg_type);
        }

        $message->delete();

        return response()->json(['message' => 'Pesan berhasil dihapus.'], 200);
    }
}
