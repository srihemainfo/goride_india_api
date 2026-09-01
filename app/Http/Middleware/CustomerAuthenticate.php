<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Models\customer_register;

class CustomerAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Extract Bearer Token from request
        $rawToken = $request->bearerToken();

        if (!$rawToken) {
            Log::warning('[CustomerAuthenticate] Authorization bearer token missing.');
            return response()->json([
                'status'  => false,
                'message' => 'Authorization token missing.'
            ], 401);
        }

        try {
            // 2. Extract plain token hash (handles Sanctum's "id|token" format)
            $hashedToken = $rawToken;
            if (str_contains($rawToken, '|')) {
                [$id, $hashedToken] = explode('|', $rawToken, 2);
            }
            $tokenHash = hash('sha256', $hashedToken);

            // 3. Find token in 'global_auth' connection first, then fallback to 'mysql'
            $activeConnection = 'global_auth';
            $glo_st = true;
            $tokenRecord = DB::connection('global_auth')
                ->table('personal_access_tokens')
                ->where('token', $tokenHash)
                ->first();
                

            if (!$tokenRecord) {
                $tokenRecord = DB::connection('mysql')
                    ->table('personal_access_tokens')
                    ->where('token', $tokenHash)
                    ->first();

                if ($tokenRecord) {
                    $activeConnection = 'mysql';
                    $glo_st = false;
                }
            }

            // 4. Validate token existence and expiration
            if (!$tokenRecord || ($tokenRecord->expires_at && now()->greaterThan($tokenRecord->expires_at))) {
                Log::warning('[CustomerAuthenticate] Token is invalid or expired.', ['token_hash' => $tokenHash]);
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized customer access.'
                ], 401);
            }

            // 5. Query user details based on the active connection source
            $userTable = ($activeConnection === 'mysql') ? 'customer_register' : 'user_register';

            $userRecord = DB::connection($activeConnection)
                ->table($userTable)
                ->where('id', $tokenRecord->tokenable_id)
                ->first();

            if (!$userRecord) {
                Log::error('[CustomerAuthenticate] User account not found for valid token.', [
                    'connection' => $activeConnection,
                    'user_id'    => $tokenRecord->tokenable_id
                ]);
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid user payload received.'
                ], 401);
            }

            // 6. Inline Migration: Move user & token from 'mysql' to 'global_auth' if sourced from legacy DB
            if ($activeConnection) {
                $userData = (array) $userRecord;

                DB::connection('global_auth')->transaction(function () use ($userData, $tokenRecord, &$userRecord, $activeConnection) {
                    $email  = $userData['email'] ?? null;
                    $mobile = $userData['mobile'] ?? null;
                
                    // 1. Identify existing record or prepare unique search keys
                    $matchCriteria = array_filter([
                        'mobile' => $mobile,
                        'email'  => $email,
                    ]);
                
                    // Fallback if both email and mobile are missing
                    if (empty($matchCriteria)) {
                        $matchCriteria = ['email' => 'fallback_' . Str::uuid() . '@local.com'];
                    }
                
                    // 2. Perform updateOrInsert on user_register
                    $existingUser = DB::connection('global_auth')
                        ->table('user_register')
                        ->where(function ($query) use ($mobile, $email) {
                            if ($mobile) $query->where('mobile', $mobile);
                            if ($email) $query->orWhere('email', $email);
                        })
                        ->first();
                
                    if ($existingUser && ($existingUser->first_name == '' || $existingUser->first_name == null)) {
                        
                        // dd('hi');
                        
                        $userModel = customer_register::where('uuid', $existingUser->uuid)
                            ->orWhere(function ($query) use ($email, $mobile) {
                                if ($email) $query->where('email', $email);
                                if ($mobile) $query->orWhere('mobile', $mobile);
                            })->where(['deletes' => 0, 'status' => 0])->first();
                            
                        // Update existing record
                        DB::connection('global_auth')
                            ->table('user_register')
                            ->where('id', $existingUser->id)
                            ->update([
                                'first_name' => $userModel->name??null,
                                'email' => $userModel->email??null,
                                'updated_at' => now(),
                            ]);
                
                        $userRecord = DB::connection('global_auth')
                            ->table('user_register')
                            ->where('id', $existingUser->id)
                            ->first();
                    } else if(!$existingUser && $activeConnection == 'mysql') {
                        // Insert new record
                        $newUuid = (string) Str::uuid();
                        $getId = DB::connection('global_auth')
                            ->table('user_register')
                            ->insertGetId([
                                'uuid'            => $newUuid,
                                'first_name'      => $userData['name'] ?? $userData['first_name'] ?? null,
                                'email'           => $email,
                                'mobile'          => $mobile,
                                'mobile_verified' => 1,
                                'firebase_uid'    => null,
                                'login_provider'  => 'mobile',
                                'created_at'      => now(),
                                'updated_at'      => now(),
                            ]);
                
                        $userRecord = DB::connection('global_auth')
                            ->table('user_register')
                            ->where('id', $getId)
                            ->first();
                    }
                
                    // 3. Insert or update the personal_access_token mapped to global_auth user ID
                    $tokenData = (array) $tokenRecord;
                    unset($tokenData['id']);
                
                    $tokenData['tokenable_type'] = 'App\Models\UserRegister';
                    $tokenData['tokenable_id']   = $userRecord->id;
                
                    DB::connection('global_auth')
                        ->table('personal_access_tokens')
                        ->updateOrInsert(
                            ['token' => $tokenRecord->token],
                            $tokenData
                        );
                });

                Log::info('[CustomerAuthenticate] Migrated user and token to global_auth successfully.');
            }

            // 7. Local database check by UUID or runtime hydration fallback
            $userDataArray = (array) $userRecord;
            $uuid = $userDataArray['uuid'] ?? null;
            $email = $userDataArray['email'] ?? null;
            $mobile = $userDataArray['mobile'] ?? null;

            // $userModel = $uuid ? customer_register::where('uuid', $uuid)->first() : null;
            $userModel = customer_register::where('uuid', $uuid)
                ->orWhere(function ($query) use ($email, $mobile) {
                    if ($email) $query->where('email', $email);
                    if ($mobile) $query->orWhere('mobile', $mobile);
                })->where(['deletes' => 0, 'status' => 0])->first();

            if ($userModel) {
                Log::info('[CustomerAuthenticate] Found existing customer locally.', ['customer_id' => $userModel->id]);
            } else {
                Log::info('[CustomerAuthenticate] Customer missing locally. Hydrating dynamic model.', ['uuid' => $uuid]);
                
                $userModel = new customer_register();
                $userModel->forceFill($userDataArray);
                $userModel->exists = true;
            }

            // 8. Bind user model to Auth facade, Request Resolver, and Request Attributes
            auth()->setUser($userModel);

            $request->setUserResolver(function () use ($userModel) {
                return $userModel;
            });

            $request->attributes->set('customer', $userModel);

        } catch (\Exception $e) {
            Log::error('[CustomerAuthenticate] Execution exception.', [
                'error_message' => $e->getMessage(),
                'trace'         => $e->getFile() . ':' . $e->getLine()
            ]);

            return response()->json([
                'status'  => false,
                'error'   => $e->getMessage(),
                'message' => 'Authentication processing failed.'
            ], 401);
        }

        return $next($request);
    }
}