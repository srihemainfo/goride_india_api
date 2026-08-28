<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckBlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // gets authenticated user via Sanctum

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Check if user is blocked
        $blocked = DB::table('blocked_user')
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->where('expiry_date', '>=', \Carbon\Carbon::now())
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->first();


        if ($blocked) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is blocked until ' . Carbon::parse($blocked->expiry_date)->format('d-m-Y')
            ], 200);
        }

        return $next($request);
    }
}
