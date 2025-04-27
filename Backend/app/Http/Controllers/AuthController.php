<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('Login request received', ['data' => $request->all()]);

            // Validasi input
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            // Cari user berdasarkan username
            $user = User::where('username', $request->username)->first();

            // Cek apakah user ada dan password benar
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Username atau password salah'], 401);
            }

            // Hapus semua token lama yang mungkin ada
            $user->tokens()->delete();

            // Membuat token baru
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log success login
            Log::info('User logged in successfully', ['user_id' => $user->id]);

            // Kembalikan response dengan token dan data user
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'role' => $user->role,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Log error jika terjadi kegagalan
            Log::error('Login failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan di server', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Ambil token dari request
            $request->user()->tokens->each(function ($token) {
                $token->delete(); // Hapus token yang terkait dengan user
            });

            // Kembalikan response sukses
            return response()->json(['message' => 'Logout berhasil'], 200);
        } catch (\Exception $e) {
            // Log error jika gagal logout
            Log::error('Logout failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat logout', 'error' => $e->getMessage()], 500);
        }
    }
}
