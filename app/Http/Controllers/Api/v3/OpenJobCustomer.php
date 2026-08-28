<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BidPlacedToRedis;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Api\v3\OpenJobsController;

class OpenJobCustomer extends Controller
{
    public function formatReadableDate($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->format('d M Y, h:i A');
    }
    
    // public function cheap_price($req_arr)
    // {
    //     $fromWords = preg_split('/[\s,]+/', strtolower($req_arr['req']['from']), -1, PREG_SPLIT_NO_EMPTY);
    
    //     $result = [];
    
    //     foreach ($req_arr['dates'] as $checkDate) {
    
    //         $query = DB::table('schedule_dates as sd')
    //             ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
    //             ->select(
    //                 'sd.id',
    //                 'ur.name',
    //                 'sd.from_place',
    //                 'sd.to_place',
    //                 // DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sd.dates_price, '$.\"{$checkDate}\"')) AS price"),
    //                 DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(sd.dates_price, '$.\"{$checkDate}\"')) AS DECIMAL(10,2)) AS numeric_price")
    //             )
    //             ->where('sd.deletes', 0);
    
    //         // From place match
    //         $query->where(function ($q) use ($fromWords) {
    //             foreach ($fromWords as $word) {
    //                 $q->orWhere('sd.from_place', 'LIKE', "%$word%");
    //             }
    //         });
    
    //         $query->whereRaw("JSON_CONTAINS(JSON_KEYS(sd.dates_price), JSON_QUOTE(?))", [$checkDate]);
    
    //         $drivers = $query->orderBy('numeric_price')->get();
    
    //         $result[] = [
    //             'date' => $checkDate,
    //             'driver_count' => $drivers->count(),
    //             'cheapest_price' => $drivers->count() ? $drivers->first()->price : null,
    //             'drivers' => $drivers
    //         ];
    //     }
    
    //     return $result;
    // }
    
    public function cheap_price($req_arr)
    {
        $fromWords = preg_split('/[\s,]+/', strtolower($req_arr['req']['from']), -1, PREG_SPLIT_NO_EMPTY);
        $jobType = strtolower($req_arr['req']['job_type']); // "oneway" or "return"
    
        $result = [];
    
        foreach ($req_arr['dates'] as $checkDate) {

            $jsonPath = "$.\"{$checkDate}\".\"{$jobType}\"";
    
            $query = DB::table('schedule_dates as sd')
                ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
                ->select(
                    'sd.id as sch_id',
                    'ur.name',
                    'sd.from_place',
                    'sd.to_place',
                    // DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sd.dates_price, '$jsonPath')) AS price"),
                    DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(sd.dates_price, '$jsonPath')) AS DECIMAL(10,2)) AS numeric_price"),
                    'ur.vehicle_details'
                )
                ->where('sd.deletes', 0);
    
            $query->where(function ($q) use ($fromWords) {
                foreach ($fromWords as $word) {
                    $q->orWhere('sd.from_place', 'LIKE', "%$word%");
                }
            });
    
            $query->whereRaw("JSON_EXTRACT(sd.dates_price, '$.\"{$checkDate}\"') IS NOT NULL");
    
            $drivers = $query->orderBy('numeric_price', 'ASC')->get();
    
            $drivers = $drivers->map(function ($d) {
                $d->price = $d->numeric_price !== null ? (float)$d->numeric_price : null;
                unset($d->numeric_price);
                return $d;
            });
    
            $result[] = [
                'date' => $checkDate,
                'driver_count' => $drivers->count(),
                'cheapest_price' => $drivers->count() ? $drivers->first()->price : null,
                'drivers' => $drivers
            ];
        }
    
        return $result;
    }
    
    public function fetch_journey(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'from'    => ['required', 'string'],
                'to'      => ['required', 'string'],
                'job_type' => ['required', 'string'],
                'pickup'  => ['required', 'date_format:Y-m-d'],
                'dropoff' => ['required', 'date_format:Y-m-d'],
            ]);
    
            $pickup = $validated['pickup'];
    
            // Next 7 days
            $datesToCheck_arr = [];
            for ($i = 0; $i < 7; $i++) {
                $datesToCheck_arr[] = date('Y-m-d', strtotime("$pickup +$i day"));
            }
    
            $req_arr = [
                'req' => $request->all(),
                'dates' => $datesToCheck_arr
            ];
    
            $next7days = $this->cheap_price($req_arr);
            
            $requested_date_data = collect($next7days)->firstWhere('date', $pickup);
            
            return response()->json([
                'status' => true,
                'requested_date_drivers' => $requested_date_data['drivers'],
                'next_7_days_summary' => $next7days,
                'message' => "Price summary generated."
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
