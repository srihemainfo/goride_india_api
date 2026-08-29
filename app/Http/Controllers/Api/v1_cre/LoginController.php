<?php

namespace App\Http\Controllers\Api\v1_cre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\user_register;

class LoginController extends Controller
{
    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user'      => 'required|string',
            'pass'      => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid username or password'
            ], 422);
        }

        $email    = trim((string) $request->input('user'));
        $rawPass  = (string) $request->input('pass');
        $fcmToken = $request->filled('fcm_token') ? trim((string) $request->input('fcm_token')) : null;

        $throttleKey = 'login_cre:' . Str::lower($email) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => false,
                'message' => 'Too many login attempts. Please try again after ' . $seconds . ' seconds.'
            ], 429);
        }

        try {
            $user = DB::table('user_register')
                ->select(['id', 'roll_id', 'email', 'pass', 'status', 'browser_fcm_token'])
                ->where('email', $email)
                ->where('roll_id', '3')
                ->where('deletes', '0')
                ->orderBy('id', 'asc')
                ->first();

            if (!$user) {
                RateLimiter::hit($throttleKey, 60);
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid username or password'
                ], 401);
            }

            $md5Hash = md5($rawPass);
            $isValid = !empty($user->pass) && (hash_equals($user->pass, $md5Hash) || Hash::check($rawPass, $user->pass));

            if (!$isValid) {
                RateLimiter::hit($throttleKey, 60);
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid username or password'
                ], 401);
            }

            if ($user->status == 1) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Your Account is In Active'
                ], 403);
            }

            RateLimiter::clear($throttleKey);

            if ($fcmToken !== null && $user->browser_fcm_token !== $fcmToken) {
                DB::table('user_register')
                    ->where('id', $user->id)
                    ->update(['browser_fcm_token' => $fcmToken]);
            }

            $userModel = user_register::find($user->id);
            $token = $userModel ? $userModel->createToken('CREaccessToken')->plainTextToken : null;

            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'data'    => [
                    'id'      => $user->id,
                    'roll_id' => $user->roll_id,
                    'email'   => $user->email,
                    'token'   => $token,
                ]
            ], 200);

        } catch (\Throwable $e) {
            Log::error('CRE LoginController Exception: ' . $e->getMessage(), [
                'email' => $email,
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'An error occurred during authentication. Please try again.'
            ], 500);
        }
    }
}