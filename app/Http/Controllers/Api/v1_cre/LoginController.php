<?php

namespace App\Http\Controllers\Api\v1_cre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'status' => false,
            'message' => 'Invalid username or password'
        ], 422);
    }

    $email = $request->input('user');
    $password = md5($request->input('pass'));

    $user = DB::table('user_register')
        ->where('email', $email)
        ->where('email', '!=', '')
        ->where('pass', '!=', '')
        ->where('pass', $password)
        ->where('roll_id', '=', '3')
        ->where('deletes', '0')
        ->orderBy('id', 'asc')
        ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid username or password'
        ], 401);
    }

    if ($user->status == 1) {
        return response()->json([
            'status' => false,
            'message' => 'Your Account is In Active'
        ], 403);
    }

   
    if ($request->filled('fcm_token')) {
        DB::table('user_register')
            ->where('id', $user->id)
            ->update(['browser_fcm_token' => $request->input('fcm_token')]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'data' => [
            'id' => $user->id,
            'roll_id' => $user->roll_id,
            'email' => $user->email,
        ]
    ], 200);
}

}