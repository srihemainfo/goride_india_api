<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class MultiAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json([
                'status' => false,
                'message' => 'Token missing'
            ], 401);
        }

        // Sanctum token format: id|token
        $token = PersonalAccessToken::findToken($bearer);

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token'
            ], 401);
        }

        // This automatically resolves correct model
        $user = $token->tokenable;

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 401);
        }

        // attach authenticated model to request
        $request->merge(['auth_user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}