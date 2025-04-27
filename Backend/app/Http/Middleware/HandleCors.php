<?php
namespace App\Http\Middleware;

use Closure;

class HandleCors
{
    public function handle($request, Closure $next)
    {
        // Handle OPTIONS request (preflight)
        if ($request->getMethod() == "OPTIONS") {
            return response()->json([], 200);
        }

        $response = $next($request);

        // Set CORS headers for the response
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true'); // Allow credentials (cookies, etc.)

        return $response;
    }

}
