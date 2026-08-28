<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SocietyJobController extends Controller
{
    
    public function encryptJobId($id, $key = 12345)
    {
        // Step 1: XOR
        $xored = $id ^ $key;
    
        // Step 2: Convert to string (important!)
        $stringValue = (string) $xored;
    
        // Step 3: Base64 encode
        $base64 = base64_encode($stringValue);
    
        // Step 4: Make URL safe
        return rtrim(strtr($base64, '+/', '-_'), '=');
    }
    
    public function decryptJobId($encoded, $key = 12345) {
        $decoded = base64_decode(strtr($encoded, '-_', '+/'));
        return $decoded ^ $key;
    }
    
    // public function jobs(Request $request)
    // {
    //     // Added validation rules for your filters to prevent unexpected SQL gaps
    //     $validator = Validator::make($request->all(), [
    //         'page'     => 'nullable|integer|min:1',
    //         'per_page' => 'nullable|integer|min:1|max:100',
    //         'type'     => 'nullable|string|in:group,my,all',
    //         'group_id' => 'required_if:type,group|integer', 
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => $validator->errors()->first(),
    //             'data'    => (object)[]
    //         ], 422);
    //     }

    //     try {
    //         $user     = auth()->user();
    //         $userId   = $user->id;
    //         $page     = $request->page ?? 1;
    //         $perPage  = $request->per_page ?? 20;
    //         $offset   = ($page - 1) * $perPage;

    //         $groups = $this->getUserGroupIds($userId);

    //         if (empty($groups)) {
    //             return response()->json([
    //                 'status'  => true,
    //                 'message' => 'No jobs found.',
    //                 'data'    => [
    //                     'jobs' => []
    //                 ]
    //             ]);
    //         }

    //         $query = $this->baseQuery($groups);
            
    //         $this->applyFilters($query, $request);

    //         $total = (clone $query)->count();

    //         // Fixed: Realigned aliases perfectly with the baseQuery definitions
    //         $jobs = $query
    //             ->select([
    //                 'gjs.id',
    //                 'gjs.group_id',
    //                 'gjs.created_at as shared_at',
    //                 'sg.name as group_name',
    //                 'sg.image as group_image',
    //                 'j.id as job_id',            // Matches 'j' alias now
    //                 'j.from_place',
    //                 'j.to_place',
    //                 'j.pickup_date',
    //                 // 'j.pickup_time',
    //                 'j.global_type',
    //                 // 'j.ride_type',
    //                 'j.fare',
    //                 // 'j.status',
    //                 'c.id as customer_id',        // Matches 'c' alias now
    //                 'c.name as customer_name',
    //                 'c.profile_img_url'
    //             ])
    //             ->orderByDesc('gjs.created_at')
    //             ->offset($offset)
    //             ->limit($perPage)
    //             ->get();
                
    //         foreach ($jobs as $job) {
    //             $job->group = (object)[
    //                 'id'   => $job->group_id,
    //                 'name' => $job->group_name,
    //                 'image'=> $job->group_image
    //             ];
                
    //             unset($job->group_id, $job->group_name, $job->group_image);
    //         }

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Jobs fetched successfully.',
    //             'data'    => [
    //                 'current_page'  => (int)$page,
    //                 'per_page'      => (int)$perPage,
    //                 'total_records' => $total,
    //                 'total_pages'   => ceil($total / $perPage),
    //                 'jobs'          => $jobs
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error($e);

    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage(), // Kept production-safe messaging
    //             'data'    => (object)[]
    //         ], 500);
    //     }
    // }
    
    public function jobs(Request $request)
    {
        // Added validation rules for your filters to prevent unexpected SQL gaps
        $validator = Validator::make($request->all(), [
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'type'     => 'nullable|string|in:group,my,all',
            'group_id' => 'required_if:type,group|integer', 
            'seat'     => 'nullable|integer|min:1', // Added filter validation safely
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'data'    => (object)[]
            ], 422);
        }
    
        try {
            $user     = auth()->user();
            $userId   = $user->id;
            $page     = $request->page ?? 1;
            $perPage  = $request->per_page ?? 20;
            $offset   = ($page - 1) * $perPage;
    
            $groups = $this->getUserGroupIds($userId);
    
            if (empty($groups)) {
                return response()->json([
                    'status'  => true,
                    'message' => 'No jobs found.',
                    'data'    => [
                        'jobs' => []
                    ]
                ]);
            }
    
            $query = $this->baseQuery($groups, $userId);
            
            $this->applyFilters($query, $request);
    
            $total = (clone $query)->count();
    
            $jobs = $query
                ->select([
                    // 'gjs.id',
                    'gjs.group_id',
                    'gjs.created_at as shared_at',
                    'sg.name as group_name',
                    'sg.image as group_image',
                    
                    'j.id',
                    'j.from_place',
                    'j.to_place',
                    'j.from_place_id',
                    'j.to_place_id',
                    'j.pickup_date',
                    
                    'j.global_type',
                    'j.fare',
                    'j.pass_count as total_seats',
                    // 'j.available_seats',
                    'j.isLock',
                    'j.route_id',
                    'j.stops_json',
                    'c.id as customer_id',        // Matches 'c' alias now
                    'c.name as customer_name',
                    'c.profile_img_url as pro_image',
                    
                    DB::raw("IFNULL(
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                c.vehicle_details,
                                '$.choosed_vehicle'
                            )
                        ),
                        ''
                    ) as choosed_vehicle"),
                    
                    'ro.summary',
                    'ro.polyline',
                    'ro.distance_meters',
                    'ro.duration_seconds',
                    
                    'c.fcm_token'
                ])
                ->orderByDesc('gjs.created_at')
                ->offset($offset)
                ->limit($perPage)
                ->get();
                
            // return $jobs;
                
            $jobIds = $jobs->pluck('id');
            
            $deep = env('DEEPLINK_CUSTOMER');
            
            $pendingInvites = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'pending')
                ->where('inviter_id', $userId)
                ->pluck('job_id')
                ->toArray();
    
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'accepted')
                ->selectRaw('MAX(id) as id')
                ->groupBy('job_id', DB::raw("
                    CASE 
                        WHEN type = 'join' THEN inviter_id 
                        ELSE invitee_user_id 
                    END
                "))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->select('job_id', DB::raw('COUNT(*) as booked_seats'))
                ->groupBy('job_id')
                ->pluck('booked_seats', 'job_id');
    
            // ✅ STEP 4: Passenger profiles
            $passengerList = DB::table('invitations as i')
                ->join('customer_register as c', function ($join) {
                    $join->on('c.id', '=', DB::raw("
                        CASE 
                            WHEN i.type = 'join' THEN i.inviter_id 
                            ELSE i.invitee_user_id 
                        END
                    "));
                })
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    'c.id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token'
                )
                ->get()
                ->groupBy('job_id');
                
            
            // We accumulate filtered items into a new collection because we filter dynamically inside the loop
            $filteredJobs = collect();
                
            foreach ($jobs as $job) {
                $job->group = (object)[
                    'id'   => $job->group_id,
                    'name' => $job->group_name,
                    'image'=> $job->group_image
                ];
                
                unset($job->group_id, $job->group_name, $job->group_image);
                
                $bookedSeats   = $participants[$job->id] ?? 0;
                $availableSeats = max(0, $job->total_seats - $bookedSeats);
    
                // ✅ Seat filter: safely skip to next using continue
                if ($availableSeats < 1) {
                    continue;
                }
    
                $passengers = $passengerList[$job->id] ?? collect();
                
                // return $passengers;
                
                if ($passengers->contains('id', $userId)) {
                    continue;
                }
                
                $encryptedId = $this->encryptJobId($job->id);
                
                // Map your key data properties onto the $job object contextually
                $job->total_seats = (int) $job->total_seats;
                $job->available_seats = (int) $availableSeats;
                $job->is_requested = in_array($job->id, $pendingInvites);
    
                // Placeholders
                $job->pickup_distance_km = 0;
                $job->drop_distance_km   = 0;
    
                $job->passenger_count = $passengers->count();
    
                $job->passengers = $passengers->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'profile_img_url' => $p->profile_img_url,
                    'fcm_token' => $p->fcm_token
                ])->values();
                
                $job->deepLink = $deep . '/carpool?jid=' . $encryptedId;
    
                $filteredJobs->push($job);
            }
    
            return response()->json([
                'status'  => true,
                'message' => 'Jobs fetched successfully.',
                'data'    => [
                    'current_page'  => (int)$page,
                    'per_page'      => (int)$perPage,
                    'total_records' => $total,
                    'total_pages'   => ceil($total / $perPage),
                    'jobs'          => $filteredJobs->values()
                ]
            ]);
    
        } catch (\Exception $e) {
            Log::error($e);
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => (object)[]
            ], 500);
        }
    }
    
    private function getUserGroupIds($userId)
    {
        return DB::table('group_members')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->pluck('group_id')
            ->toArray();
    }
    
    private function baseQuery(array $groupIds, $userId)
    {
        
        return DB::table('group_job_shares AS gjs')
            ->join('society_groups AS sg', 'sg.id', '=', 'gjs.group_id')
            ->join('cus_job_temp AS j', 'j.id', '=', 'gjs.job_id')
            ->join('customer_register AS c', 'c.id', '=', 'j.user_id')
            ->leftJoin(
                'route_options as ro',
                'ro.id',
                '=',
                'j.route_id'
            )
            ->where('gjs.status', 'active')
            ->whereNot('j.user_id', $userId)
            ->whereIn('gjs.group_id', $groupIds)
            ->whereNull('sg.deleted_at');
    }
    
    private function applyFilters($query, $request)
    {
        switch ($request->type) {
            case 'group':
                $query->where('gjs.group_id', $request->group_id);
                break;
                
            case 'my':
                $query->where('gjs.shared_by', auth()->id());
                break;
                
            default:
                break;
        }
    }
}