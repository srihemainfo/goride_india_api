<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiResponse
{
    public static function success($message, $data = [], $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message, $code = 200, $data = [])
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}