<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class CreAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status'  => false,
                'message' => 'Authorization token missing'
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid token'
            ], 401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid token user'
            ], 401);
        }

        $creUser = DB::table('user_register')
            ->where('id', $user->id)
            ->where('roll_id', '3')
            ->where('deletes', '0')
            ->first();

        if (!$creUser) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized CRE user'
            ], 403);
        }

        if ($creUser->status != 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Your Account is In Active'
            ], 403);
        }

        Auth::setUser($user);

        $request->attributes->set('cre_user', $creUser);

        return $next($request);
    }
}
