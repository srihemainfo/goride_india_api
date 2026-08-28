<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CheckTokenOwner
{
    public function handle(Request $request, Closure $next, string $model)
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

        if ($accessToken->tokenable_type !== $model) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Unauthorized token owner'
            ], 403);
        }

        $request->attributes->set('auth_user', $accessToken->tokenable);
        $request->attributes->set('auth_model', $model);

        return $next($request);
    }
}