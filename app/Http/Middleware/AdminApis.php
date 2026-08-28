<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApis
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'status'  => false,
                'message' => 'Authorization token missing',
            ], 401);
        }

        $token = trim(str_replace('Bearer', '', $authHeader));

        if ($token !== env('EXPECTED_API_TOKEN')) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid admin token',
            ], 403);
        }

        return $next($request);
    }
}