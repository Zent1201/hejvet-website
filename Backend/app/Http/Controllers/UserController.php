<?php

namespace App\Http\Controllers;

use App\Models\Konsul;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Create
    public function store(Request $request)
    {
        // Validasi data yang diterima dari request
        $validatedData = $request->validate([
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string',
            'role' => 'required|string|in:dokter,pasien,admin',
            'spesialisasi' => 'nullable|string', // Hanya untuk dokter
            'no_telp' => 'nullable|string',
            'jadwal_dokter' => 'nullable|string', // Validasi jadwal_dokter menjadi string
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi untuk foto_user
            'harga' => 'nullable|numeric', // Validasi untuk harga (hanya untuk dokter)
            'email' => 'nullable|email|unique:users,email', // Validasi untuk email
            'jamkerja_dokter' => 'nullable|string', // Validasi untuk jamkerja_dokter (hanya untuk dokter)
        ]);

        // Hash password sebelum menyimpan
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Handle file upload untuk foto_user jika ada
        if ($request->hasFile('foto_user')) {
            $file = $request->file('foto_user');
            $filename = $file->getClientOriginalName(); // Menyimpan nama file asli
            $path = $file->storeAs('uploads/users', $filename, 'public');
            $validatedData['foto_user'] = $path;
        }

        // Menyimpan jadwal_dokter sebagai string biasa (bukan JSON)
        // Langsung saja memasukkan data jadwal_dokter ke validatedData
        $validatedData['jadwal_dokter'] = $request->jadwal_dokter;

        // Menyimpan jamkerja_dokter jika role adalah dokter
        $validatedData['jamkerja_dokter'] = $request->jamkerja_dokter;

        // Membuat user baru di tabel users
        $user = User::create($validatedData);

        // Jika user adalah dokter, buat entri di tabel konsuls dengan harga
        if ($user->role === 'dokter' && isset($validatedData['harga'])) {
            Konsul::create([
                'harga' => $validatedData['harga'],
                'dokter_id' => $user->id, // Relasikan dengan ID dokter
            ]);
        }

        // Mengembalikan response JSON dengan data user yang baru dibuat
        return response()->json($user, 201);
    }





   // Fungsi untuk menampilkan semua data dokter, admin, dan pasien
    public function index()
    {
        // Query for doctors with konsul data
        $doctors = User::where('role', 'dokter')
            ->leftJoin('konsuls', 'users.id', '=', 'konsuls.dokter_id') // Join users with konsuls table
            ->select('users.*', 'konsuls.harga as harga') // Include harga from konsuls
            ->get()
            ->map(function ($doctor) {
                // Ensure foto_user path is correct for doctors
                $doctor->foto_user = $doctor->foto_user ? asset("storage/{$doctor->foto_user}") : null;
                return $doctor;
            });

        // Query for admins and ensure foto_user path is correct
        $admins = User::where('role', 'admin')
            ->select('users.*') // Only basic user information, no need for konsuls data
            ->get()
            ->map(function ($admin) {
                // Ensure foto_user path is correct for admins
                $admin->foto_user = $admin->foto_user ? asset("storage/{$admin->foto_user}") : null;
                return $admin;
            });

        // Query for patients and ensure foto_user path is correct
        $patients = User::where('role', 'pasien')
            ->select('users.*') // Only basic user information, no need for konsuls data
            ->get()
            ->map(function ($patient) {
                // Ensure foto_user path is correct for patients
                $patient->foto_user = $patient->foto_user ? asset("storage/{$patient->foto_user}") : null;
                return $patient;
            });

        // Combine all data into one response
        $data = [
            'doctors' => $doctors,
            'admins' => $admins,
            'patients' => $patients,
        ];

        return response()->json($data);
    }





    // public function show($id)
    // {
    //     return User::findOrFail($id);
    // }

    // Show By ID
    public function show($id)
    {
        $user = User::with('konsuls', 'hewans')->findOrFail($id);

        // Add `harga` only for doctors if `konsuls` exists
        if ($user->role === 'dokter' && $user->konsuls()->exists()) {
            $user->harga = $user->konsuls()->first()->harga;
        } else {
            $user->harga = null;
        }

        // Ensure `foto_user` path is correct for users with an image
        $user->foto_user = $user->foto_user ? asset("storage/{$user->foto_user}") : null;

        // Prepare role-based data
        if ($user->role === 'dokter') {
            $response = [
                'id' => $user->id,
                'nama' => $user->nama,
                'role' => $user->role,
                'spesialisasi' => $user->spesialisasi,
                'jadwal_dokter' => $user->jadwal_dokter,
                'jamkerja_dokter' => $user->jamkerja_dokter,
                'harga' => $user->harga,
                'foto_user' => $user->foto_user,
            ];
        } elseif ($user->role === 'admin') {
            $response = [
                'id' => $user->id,
                'nama' => $user->nama,
                'role' => $user->role,
                'foto_user' => $user->foto_user,
            ];
        } else { // For 'pasien' role
            // Adding hewan data for pasien
            $hewans = $user->hewans->map(function ($hewan) {
                return [
                    'id' => $hewan->id,
                    'nama_hewan' => $hewan->nama_hewan,
                    'jenis_hewan' => $hewan->jenis_hewan,
                    'tanggal_lahir' => $hewan->tanggal_lahir,
                    'foto_hewan' => $hewan->foto_hewan ? asset("storage/{$hewan->foto_hewan}") : null,
                ];
            });

            $response = [
                'id' => $user->id,
                'nama' => $user->nama,
                'role' => $user->role,
                'foto_user' => $user->foto_user,
                'hewans' => $hewans, // Add hewan data here
            ];
        }

        return response()->json($response);
    }


    // Update
    public function update(Request $request, $id)
    {
        // Find the user by ID or fail
        $user = User::findOrFail($id);

        // Validate input
        $validatedData = $request->validate([
            'username' => 'sometimes|required|string|unique:users,username,' . $user->id,
            'password' => 'sometimes|required|string|min:6',
            'nama' => 'sometimes|required|string',
            'role' => 'sometimes|required|string|in:dokter,pasien,admin',
            'spesialisasi' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'jadwal_dokter' => 'nullable|string',
            'jamkerja_dokter' => 'nullable|string', // Menambahkan validasi untuk jamkerja_dokter
            'tgl_join' => 'sometimes|required|date',
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'harga' => 'nullable|numeric', // Validation for harga (from konsuls table)
            'email' => 'nullable|email|unique:users,email,' . $user->id, // Validasi untuk email
        ]);

        // Hash the password if it's provided
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Handle file upload for foto_user if present
        if ($request->hasFile('foto_user')) {
            // Delete the old file if it exists
            if ($user->foto_user) {
                Storage::disk('public')->delete($user->foto_user);
            }

            // Store the new file
            $file = $request->file('foto_user');
            $filename = $file->getClientOriginalName(); // Keep the original file name
            $path = $file->storeAs('uploads/users', $filename, 'public');
            $validatedData['foto_user'] = $path;
        }

        // Update the user's data
        $user->update($validatedData);

        // Check if the user is a doctor
        if ($user->role === 'dokter') {
            // Check if a konsul entry already exists for this doctor
            $konsul = Konsul::where('dokter_id', $user->id)->first();

            if (isset($validatedData['harga'])) {
                if ($konsul) {
                    // Update the existing harga
                    $konsul->update(['harga' => $validatedData['harga']]);
                } else {
                    // Create a new konsul entry with harga and dokter_id
                    Konsul::create([
                        'harga' => $validatedData['harga'],
                        'dokter_id' => $user->id
                    ]);
                }
            }

            // Update jamkerja_dokter if provided
            if (isset($validatedData['jamkerja_dokter'])) {
                // Set jamkerja_dokter, ensure it's updated for the doctor
                $user->jamkerja_dokter = $validatedData['jamkerja_dokter'];
                $user->save();
            }
        }

        // Ensure this part is correctly returning harga
        $user->harga = $user->konsuls()->exists() ? $user->konsuls()->first()->harga : null;

        return response()->json($user);
    }





    // Delete

    public function destroy($id)
    {
        // Temukan user berdasarkan ID
        $user = User::findOrFail($id);

        // Periksa apakah user memiliki foto_user dan hapus file tersebut
        if ($user->foto_user) {
            Storage::disk('public')->delete($user->foto_user);
        }

        // Jika user adalah dokter, hapus entri yang terkait di tabel konsuls
        if ($user->role === 'dokter') {
            // Cari entri konsul yang terkait dengan dokter
            $konsul = Konsul::where('dokter_id', $user->id)->first();
            if ($konsul) {
                // Hapus entri konsul yang terkait
                $konsul->delete();
            }
        }

        // Hapus data user
        $user->delete();

        // Kembalikan response kosong dengan status 204 (No Content)
        return response()->json(null, 204);
    }


     // Method for admin dashboard
     public function adminDashboard()
     {
         // Logic specific to admin dashboard
         return response()->json(['message' => 'Welcome to the admin dashboard']);
     }

     // Method for doctor schedule
     public function dokterSchedule()
     {
         // Logic specific to doctor's schedule
         return response()->json(['message' => 'Here is the doctor schedule']);
     }

     // Method for patient dashboard
     public function pasienDashboard()
     {
         // Logic specific to patient dashboard
         return response()->json(['message' => 'Welcome to the patient dashboard']);
     }









}
