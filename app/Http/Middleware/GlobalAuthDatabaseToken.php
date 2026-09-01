<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserRegister;

class GlobalAuthDatabaseToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Extract the raw plain text Bearer Token from the header
        $rawToken = $request->bearerToken();

        if (!$rawToken) {
            return response()->json([
                'status'  => false,
                'message' => 'Token missing.'
            ], 401);
        }

        // Sanctum stores tokens hashed or split by a '|'. Parse it safely.
        $hashedToken = $rawToken;
        if (str_contains($rawToken, '|')) {
            [$id, $hashedToken] = explode('|', $rawToken, 2);
        }
        
        $tokenHash = hash('sha256', $hashedToken);

        // 2. First check: Look in 'global_auth' database
        $tokenRecord = DB::connection('global_auth')
            ->table('personal_access_tokens')
            ->where('token', $tokenHash)
            ->first();

        $activeConnection = 'global_auth';

        // 3. Fallback: If not found in global_auth, check 'mysql' database
        if (!$tokenRecord) {
            $tokenRecord = DB::connection('mysql')
                ->table('personal_access_tokens')
                ->where('token', $tokenHash)
                ->first();

            if ($tokenRecord) {
                $activeConnection = 'mysql';
            }
        }

        // 4. Verify if token exists and hasn't expired
        if (!$tokenRecord || ($tokenRecord->expires_at && now()->greaterThan($tokenRecord->expires_at))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        // 5. Fetch user record using table based on connection
        // mysql -> customer_register | global_auth -> user_register
        $userTable = ($activeConnection === 'mysql') ? 'customer_register' : 'user_register';

        $userRecord = DB::connection($activeConnection)
            ->table($userTable)
            ->where('id', $tokenRecord->tokenable_id)
            ->first();

        if (!$userRecord) {
            return response()->json([
                'status'  => false,
                'message' => 'User account not found.'
            ], 401);
        }

        // 6. Migration Step: If found in 'mysql', copy User and Token to 'global_auth'
        if ($activeConnection === 'mysql') {
            $userData = (array) $userRecord;

            DB::connection('global_auth')->transaction(function () use ($userData, $tokenRecord, &$userRecord) {
                
                // Insert user into global_auth.user_register
                $getId = DB::connection('global_auth')
                    ->table('user_register')
                    ->insertGetId([
                        'uuid'            => (string) Str::uuid(),
                        'first_name'      => $userData['mobile'] ?? null,
                        'email'           => $userData['mobile'] ?? null,
                        'mobile'          => $userData['mobile'] ?? null,
                        'mobile_verified' => 1,
                        'firebase_uid'    => null,
                        'login_provider'  => 'mobile',
                        'created_at'      => now(),
                        'updated_at'      => now()
                    ]);

                // Prepare token payload matching global_auth schema format
                $tokenData = (array) $tokenRecord;
                $tokenData['tokenable_type'] = 'App\Models\UserRegister';
                $tokenData['tokenable_id']   = $getId;

                // Insert token into global_auth.personal_access_tokens
                DB::connection('global_auth')
                    ->table('personal_access_tokens')
                    ->updateOrInsert(
                        ['token' => $tokenRecord->token],
                        $tokenData
                    );

                // Update userRecord reference to reflect the newly created global_auth user
                $userRecord = DB::connection('global_auth')
                    ->table('user_register')
                    ->where('id', $getId)
                    ->first();
            });
        }

        // 7. Hydrate the model dynamically and bind to 'global_auth'
        $userModel = new UserRegister();
        $userModel->setConnection('global_auth');
        $userModel->forceFill((array) $userRecord);
        $userModel->exists = true;

        auth()->setUser($userModel);

        return $next($request);
    }
}