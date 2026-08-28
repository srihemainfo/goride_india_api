<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class DriverAuthMiddleware
{
    
    public function handle(Request $request, Closure $next)
    {

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Authorization token missing'
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Invalid token'
            ], 401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Invalid token user'
            ], 401);
        }

        $customer = DB::table('user_register')
            ->where('id', $user->id)
            // ->where('status', '0')
            ->where('deletes', '0')
            ->first();

        if (!$customer) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $customer = DB::table('user_register')
            ->where('id', $user->id)
            ->where('status', 0)
            // ->where('deletes', '0')
            ->first();

        if (!$customer) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Account Suspended'
            ], 403);
        }
        
        Auth::setUser($user);

        $request->attributes->set('driver', $customer);

        return $next($request);
    }
}