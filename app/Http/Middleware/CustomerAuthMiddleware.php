<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class CustomerAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Get Bearer token
        $token = $request->bearerToken();
        
        // dd($token);
        // dd($token);

        if (!$token) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Authorization token missing'
            ], 401);
        }

        // 2. Validate Sanctum token
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Invalid token'
            ], 401);
        }

        // 3. Get token owner (customer_register model)
        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Invalid token user'
            ], 401);
        }

        $customer = DB::table('customer_register')
            ->where('id', $user->id)
            ->where('status', '0')
            ->where('deletes', '0')
            ->first();

        if (!$customer) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Unauthorized customer'
            ], 403);
        }
        
        Auth::setUser($user);

        // 5. Attach customer safely (NO merge → fixes your error)
        $request->attributes->set('customer', $customer);

        return $next($request);
    }
}