<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use App\Models\ApiRequest;
use DB;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {

        // Change the environment variable value
        // putenv('Base_URL=' . $request->getSchemeAndHttpHost() . '\\');

        // Extract request data
        $requestData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'parameters' => $request->all(),
            'headers' => $request->header(),
        ];

        // Store the request in the database
        // ApiRequest::create($requestData);

        $insertData = [
            'ip' => $request->ip(),
            'type' => $request->route()->getName() ?? 'LogApiRequests',
            'userid' => 0,
            'email' => '',
            'mobile' => '',
            'message' => 'API Log',
            // 'message' => env('Base_URL'),
            'request' => json_encode($requestData),
            'path' => __DIR__,
            'file_name' => basename(__FILE__),
            'line_no' => __LINE__,
            'createdon' => now(),
        ];

        $inserted = DB::table('error_log')->insert($insertData);

        if ($inserted) {
            return $next($request);
        } else {
            return route('logError');
            // return response()->json(['status' => 'failed', 'message' => 'Log Process failed!', 'error' => 'Kindly contact the admin team!']);
        }
    }
}
