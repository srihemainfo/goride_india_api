<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class userLocationLog
{
    public static function logUserActivity($userId, $module, $action, $rideId = null, $meta = [])
    {
        try {
            $now = Carbon::now();

            DB::table('user_activity_log')->insert([
                'user_id'     => $userId,
                'module'      => $module,
                'action'      => $action,
                'ride_id'     => $rideId,
                'meta'        => json_encode($meta),
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'created_at'  => $now
            ]);

        } catch (\Throwable $e) {

            // Log minimal but useful debug info
            Log::error('UserActivityLog Failed', [
                'error'   => $e->getMessage(),
                'user_id' => $userId,
                'module'  => $module,
                'action'  => $action,
                'meta'    => $meta
            ]);

            // Optional: silently fail (recommended for logging systems)
            // Do NOT throw again unless this is critical
        }
    }
}