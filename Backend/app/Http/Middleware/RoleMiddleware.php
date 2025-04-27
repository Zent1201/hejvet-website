<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Pastikan pengguna sudah login dan role sesuai
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Jika parameter role tidak diberikan atau tidak sesuai
        if ($role && Auth::user()->role !== $role) {
            return response()->json(['message' => 'Unauthorized, role not permitted'], 403);
        }

        return $next($request);
    }
}
