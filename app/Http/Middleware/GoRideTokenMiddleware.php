<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\GoRideToken;

class GoRideTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Goride-Token');

        if (!$token || !GoRideToken::validate($token)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        return $next($request);
    }
}