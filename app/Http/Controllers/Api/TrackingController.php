<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BidPlacedToRedis;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Support\Facades\Redis;
use App\Services\NotificationService;
use App\Services\PusherService;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use App\Helpers\userLocationLog;
use Illuminate\Support\Facades\Cache;

class TrackingController extends Controller
{
    /**
     * GET /tracking/{tripId}?token=AUTH_TOKEN
     *
     * Data flow:
     *   cus_job_temp  (or job table)
     *     └─ invitation table  →  inviter_id  (passenger_id)
     *         └─ customer_register  →  lat, lng + other passenger data
     *
     * We also grab the assigned driver + vehicle for the UI panel.
     */
    public function show(Request $request, int $tripId)
    {
        // ── 1. Auth token (route param or session/auth guard) ────────────────
        $customerToken = $request->query('token')
                      ?? session('customer_token')
                      ?? '2ZD3cFzUOxpk9mvarc7lWd6YkAnWHzdDlDHa4oXxd045052b';
 
        // ── 2. Fetch trip ────────────────────────────────────────────────────
        $trip = DB::table('cus_job_temp')
                    ->where('id', $tripId)
                    ->first();
 
        abort_if(!$trip, 404, 'Trip not found');
 
        // ── 3. Get passenger_id from invitation table ────────────────────────
        $invitation = DB::table('invitations')
                        ->where('job_id', $tripId)   // adjust FK column if different
                        ->first();
 
        $passengerId = $invitation->inviter_id ?? null;
 
        // ── 4. Get passenger data (lat / lng) from customer_register ─────────
        $passenger = null;
        if ($passengerId) {
            $passenger = DB::table('customer_register')
                            ->where('id', $passengerId)
                            ->select('id', 'name', 'mobile', 'lat', 'lng', 'profile_img_url')
                            ->first();
        }
 
        // ── 5. Get assigned driver ───────────────────────────────────────────
        $driver = null;
        if (!empty($trip->user_id)) {
            $driver = DB::table('user_register')   // adjust table name if needed
                         ->where('id', $trip->user_id)
                         ->select('id', 'name', 'mobile', 'profile_img_url', 'ratings')
                         ->first();
        }
 
        // ── 6. Get vehicle ───────────────────────────────────────────────────
        $vehicle = null;
        if (!empty($trip->vehicle_id)) {
            // $vehicle = DB::table('vehicles')          // adjust table name if needed
            //               ->where('id', $trip->vehicle_id)
            //               ->select('id', 'model', 'number_plate', 'color')
            //               ->first();
        }
        
        // dd($tripId, $trip, $passenger, $driver, $vehicle, $customerToken);
 
        return view('pages.livetracking', compact(
            'tripId',
            'trip',
            'passenger',
            'driver',
            'vehicle',
            'customerToken',
        ));
    }
}