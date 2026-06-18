<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization') ?? $request->query('token');
        
        // Remove Bearer prefix if exists
        $token = str_replace('Bearer ', '', $token);

        // Very basic token auth as per request.
        // In production, this should be verified against a users/tokens table.
        $validToken = env('API_TOKEN', 'pep-dttot-secret-token');

        if (!$token || $token !== $validToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API Token.'
            ], 401);
        }

        return $next($request);
    }
}
