<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // Log::warning('[GlobalAuthMiddleware] Token missing in authorization header.');
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
            // Log::info('[GlobalAuthMiddleware] Token not found in global_auth. Checking mysql connection...', ['token_hash' => $tokenHash]);
            
            $tokenRecord = DB::connection('mysql')
                ->table('personal_access_tokens')
                ->where('token', $tokenHash)
                ->first();

            if ($tokenRecord) {
                $activeConnection = 'mysql';
                // Log::info('[GlobalAuthMiddleware] Token found in mysql connection.', ['token_id' => $tokenRecord->id]);
            }
        }

        // 4. Verify if token exists and hasn't expired
        if (!$tokenRecord || ($tokenRecord->expires_at && now()->greaterThan($tokenRecord->expires_at))) {
            // Log::warning('[GlobalAuthMiddleware] Token invalid or expired.', ['token_hash' => $tokenHash]);
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        // 5. Fetch user record using table based on connection
        $userTable = ($activeConnection === 'mysql') ? 'customer_register' : 'user_register';

        $userRecord = DB::connection($activeConnection)
            ->table($userTable)
            ->where('id', $tokenRecord->tokenable_id)
            ->first();

        if (!$userRecord) {
            // Log::error('[GlobalAuthMiddleware] User account not found.', [
            //     'connection' => $activeConnection,
            //     'table'      => $userTable,
            //     'user_id'    => $tokenRecord->tokenable_id
            // ]);
            return response()->json([
                'status'  => false,
                'message' => 'User account not found.'
            ], 401);
        }

        // 6. Migration Step: If found in 'mysql', copy User and Token to 'global_auth'
        if ($activeConnection === 'mysql') {
            $userData = (array) $userRecord;

            try {
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

                    // Prepare token payload and remove 'id' to allow auto-increment
                    $tokenData = (array) $tokenRecord;
                    unset($tokenData['id']); 

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

                    // Log::info('[GlobalAuthMiddleware] Successfully migrated customer and token to global_auth.', [
                    //     'mysql_user_id'     => $userData['id'],
                    //     'global_auth_id'    => $getId
                    // ]);
                });
            } catch (\Exception $e) {
                // Log::error('[GlobalAuthMiddleware] Migration failed during transaction.', [
                //     'error' => $e->getMessage()
                // ]);
                
                return response()->json([
                    'status'  => false,
                    'message' => 'Authentication migration failed.'
                ], 500);
            }
        }

        // 7. Hydrate the model dynamically and bind to 'global_auth'
        $userModel = new UserRegister();
        $userModel->setConnection('global_auth');
        $userModel->forceFill((array) $userRecord);
        $userModel->exists = true;

        auth()->setUser($userModel);

        // Log::info('[GlobalAuthMiddleware] User authenticated successfully.', ['user_id' => $userModel->id]);

        return $next($request);
    }
}