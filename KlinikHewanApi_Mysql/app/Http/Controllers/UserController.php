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
        $validatedData = $request->validate([
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string',
            'role' => 'required|string|in:dokter,pasien,admin',
            'spesialisasi' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'jadwal_dokter' => 'nullable|string',
            'tgl_join' => 'required|date',
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation for foto_user
            'harga' => 'nullable|numeric', // Validation for harga (from konsuls table)
        ]);

        // Hash the password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Handle file upload for foto_user if present
        if ($request->hasFile('foto_user')) {
            $file = $request->file('foto_user');
            $filename = $file->getClientOriginalName(); // Keep the original file name
            $path = $file->storeAs('uploads/users', $filename, 'public');
            $validatedData['foto_user'] = $path;
        }

        // Create the user
        $user = User::create($validatedData);

        // If the user is a doctor, create a konsul entry with harga
        if ($user->role === 'dokter' && isset($validatedData['harga'])) {
            Konsul::create([
                'harga' => $validatedData['harga'],
                'dokter_id' => $user->id,
            ]);
        }

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

    //Show By id
    public function show($id)
    {
        $user = User::with('konsuls')->findOrFail($id);

        // Load the `harga` from the konsuls table if available
        $user->harga = $user->konsuls()->exists() ? $user->konsuls()->first()->harga : null;


        return response()->json($user);
    }


    //Update

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
            'tgl_join' => 'sometimes|required|date',
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'harga' => 'nullable|numeric', // Validation for harga (from konsuls table)
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
        }

            // Ensure this part is correctly returning harga
        $user->harga = $user->konsuls()->exists() ? $user->konsuls()->first()->harga : null;
        return response()->json($user);
    }




    // Delete
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete the foto_user if it exists
        if ($user->foto_user) {
            Storage::disk('public')->delete($user->foto_user);
        }

        $user->delete();

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
