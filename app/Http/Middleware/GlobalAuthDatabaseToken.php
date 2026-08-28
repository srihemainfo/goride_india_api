<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserRegister;
use Laravel\Sanctum\PersonalAccessToken;

class GlobalAuthDatabaseToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Extract the raw plain text Bearer Token from the header
        $hashedToken = $request->bearerToken();

        if (!$hashedToken) {
            return response()->json([
                'status'  => false,
                'message' => 'Token missing.'
            ], 401);
        }

        // Sanctum stores tokens hashed or split by a '|'. Let's parse it safely.
        if (str_contains($hashedToken, '|')) {
            [$id, $hashedToken] = explode('|', $hashedToken, 2);
        }
        
        $tokenHash = hash('sha256', $hashedToken);

        // 2. Query the personal_access_tokens table directly on the global_auth database
        $tokenRecord = DB::connection('global_auth')
            ->table('personal_access_tokens')
            ->where('token', $tokenHash)
            ->first();

        // 3. Verify if token exists and hasn't expired
        if (!$tokenRecord || ($tokenRecord->expires_at && now()->greaterThan($tokenRecord->expires_at))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        // 4. Fetch the associated Customer User from the global_auth database
        $userRecord = DB::connection('global_auth')
            ->table('user_register')
            ->where('id', $tokenRecord->tokenable_id)
            ->first();

        if (!$userRecord) {
            return response()->json([
                'status'  => false,
                'message' => 'User account not found.'
            ], 401);
        }

        // 5. Hydrate the model dynamically and push it to Laravel's runtime auth memory
        $userModel = new UserRegister();
        $userModel->setConnection('global_auth'); // Bind model connection explicitly
        $userModel->forceFill((array) $userRecord);
        $userModel->exists = true;

        auth()->setUser($userModel);

        return $next($request);
    }
}