<?php

namespace App\Http\Controllers\Api\v1_cre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CreJobsController extends Controller
{
    public function getJobList(Request $request)
    {
        try {
            $creUser = $request->get('cre_user');

            $userName = $creUser->name ?? 'CRE Agent';
            if (empty($userName)) {
                $userName = $creUser->email ?? 'CRE Agent';
            }

            $userProfile = [
                'name'   => $userName,
                'role'   => 'Customer Relationship Executive',
                'status' => 'Online',
            ];

            $rawJobs = DB::table('cus_job_temp')
                ->select([
                    'id',
                    'job_no',
                    'global_type',
                    'job_status',
                    'pick_address',
                    'drop_address',
                    'from_place',
                    'to_place',
                    'pickup_date',
                    'created_at'
                ])
                ->where('deletes', '0')
                ->whereIn('job_status', ['created', 'bidding', 'pending', 'schedule'])
                ->where(function ($q) {
                    $q->whereNull('job_no')
                      ->orWhere('job_no', 'NOT LIKE', 'GRP-%');
                })
                ->orderBy('id', 'desc')
                ->get();

            $unassignedJobs = [];

            foreach ($rawJobs as $job) {
                $jobNo = $job->job_no ?? ('GR-' . $job->id);

                // Source determination
                $source = "From Website";
                if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$job->global_type) === 'customer') {
                    $source = "From Customer App";
                } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$job->global_type) === 'driver') {
                    $source = "From Driver App";
                }

                // Badge determination
                $badge = "Regular";
                if (strtolower((string)$job->global_type) === 'schedule' || strtolower((string)$job->job_status) === 'schedule') {
                    $badge = "Schedule";
                }

                // Addresses
                $from = !empty($job->pick_address) ? $job->pick_address : ($job->from_place ?? '');
                $to   = !empty($job->drop_address) ? $job->drop_address : ($job->to_place ?? '');

                // Date & Time formatting
                $dateStr = $job->pickup_date ?? $job->created_at ?? null;
                $formattedDate = '';
                $formattedTime = '';

                if ($dateStr) {
                    try {
                        $dt = Carbon::parse($dateStr);
                        $formattedDate = $dt->format('d M Y');
                        $formattedTime = $dt->format('h:i A');
                    } catch (\Throwable $e) {
                        $formattedDate = (string) $dateStr;
                    }
                }

                $unassignedJobs[] = [
                    'job_id' => $job->id,
                    'job_no' => $jobNo,
                    'source' => $source,
                    'badge'  => $badge,
                    'from'   => $from,
                    'to'     => $to,
                    'date'   => $formattedDate,
                    'time'   => $formattedTime,
                ];
            }

            return response()->json([
                'status'           => true,
                'message'          => 'Dashboard data retrieved successfully',
                'user_profile'     => $userProfile,
                'total_unassigned' => count($unassignedJobs),
                'unassigned_jobs'  => $unassignedJobs,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getJobDetails(Request $request)
    {
        try {
            $jobId = $request->input('job_id') ?? $request->input('id');

            if (!$jobId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job ID is required'
                ], 422);
            }

            $job = DB::table('cus_job_temp')
                ->where('deletes', '0')
                ->where(function ($q) use ($jobId) {
                    $q->where('id', $jobId)
                      ->orWhere('job_no', $jobId);
                })
                ->first();

            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found'
                ], 404);
            }

            $jobNo = $job->job_no ?? ('GR-' . $job->id);

            // Source determination
            $source = "From Website";
            if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$job->global_type) === 'customer') {
                $source = "From Customer App";
            } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$job->global_type) === 'driver') {
                $source = "From Driver App";
            }

            // Badge determination
            $badge = "Regular";
            if (strtolower((string)$job->global_type) === 'schedule' || strtolower((string)$job->job_status) === 'schedule') {
                $badge = "Schedule";
            }

            // Addresses
            $from = !empty($job->pick_address) ? $job->pick_address : ($job->from_place ?? '');
            $to   = !empty($job->drop_address) ? $job->drop_address : ($job->to_place ?? '');

            // Date & Time formatting
            $dateStr = $job->pickup_date ?? $job->created_at ?? null;
            $formattedDate = '';
            $formattedTime = '';

            if ($dateStr) {
                try {
                    $dt = Carbon::parse($dateStr);
                    $formattedDate = $dt->format('d M Y');
                    $formattedTime = $dt->format('h:i A');
                } catch (\Throwable $e) {
                    $formattedDate = (string) $dateStr;
                }
            }

            // Decode user_details JSON string if present
            $details = [];
            if (!empty($job->user_details)) {
                $details = is_string($job->user_details) ? json_decode($job->user_details, true) : (array)$job->user_details;
            }
            if (!is_array($details)) $details = [];

            // Passengers & Luggage logic
            $rawPass = $job->pass_count ?? ($details['pass_count'] ?? 1);
            if (is_string($rawPass) && strtolower(trim($rawPass)) === 'mini') {
                $passengers = "Mini";
            } elseif (is_numeric($rawPass)) {
                $count = (int) $rawPass;
                $passengers = $count . ($count == 1 ? ' Passenger' : ' Passengers');
            } else {
                $passengers = (string) $rawPass;
            }

            // Luggage logic (only for Website jobs)
            $luggage = null;
            if ($source === "From Website") {
                $luggCount = (int) ($details['lugg_count'] ?? ($details['luggage'] ?? 0));
                $luggage   = $luggCount . ($luggCount == 1 ? ' Luggage' : ' Luggage');
            }

            // Vehicle Type / Cab Type logic (Do NOT fallback to job_type)
            $vehicleType = 'Saloon';
            if (!empty($details['cab_type'])) {
                $vehicleType = $details['cab_type'];
            } elseif (!empty($details['car_type'])) {
                $vehicleType = $details['car_type'];
            } elseif (!empty($job->car_type)) {
                $vehicleType = $job->car_type;
            } elseif (!empty($job->cab_type)) {
                $vehicleType = $job->cab_type;
            }

            // Job Type logic
            $rawJobType = $details['job_type'] ?? ($details['trip_type'] ?? ($job->job_type ?? 'One Way'));
            $cleanType  = strtolower(trim((string)$rawJobType));
            if ($cleanType === 'oneway') {
                $jobType = "One Way";
            } elseif ($cleanType === 'roundtrip') {
                $jobType = "Round Trip";
            } else {
                $jobType = ucfirst((string)$rawJobType);
            }

            // Special Notes
            $specialNotes = !empty($job->job_remark) ? $job->job_remark : ($job->comments ?? ($details['special_notes'] ?? ($details['notes'] ?? '')));

            // Customer Details
            $customerName   = '';
            $customerMobile = '';
            $profileImg     = null;

            $userId = (int)($job->user_id ?? 0);
            if ($userId > 0) {
                $customer = DB::table('customer_register')->where('id', $userId)->first();
                if ($customer) {
                    $customerName   = $customer->name ?? '';
                    $customerMobile = $customer->mobile ?? '';
                    $profileImg     = $customer->img_url ?? null;
                }
            }

            // Fallback for Website job customer details
            if (empty($customerName) && !empty($details)) {
                $customerName   = $details['name'] ?? 'Website Customer';
                $customerMobile = $details['mobile'] ?? '';
            }

            $cleanMobile = trim((string) $customerMobile);
            $cleanDigits = preg_replace('/[^0-9+]/', '', $cleanMobile);

            $customerDetails = [
                'name'            => $customerName ?: 'Customer',
                'mobile'          => $cleanMobile,
                'profile_img'     => $profileImg,
                'call_number'     => $cleanDigits,
                'whatsapp_number' => $cleanDigits,
            ];

            return response()->json([
                'status'  => true,
                'message' => 'Job details retrieved successfully',
                'data'    => [
                    'job_id'           => $job->id,
                    'job_no'           => $jobNo,
                    'badge'            => $badge,
                    'source'           => $source,
                    'from'             => $from,
                    'to'               => $to,
                    'date'             => $formattedDate,
                    'time'             => $formattedTime,
                    'passengers'       => $passengers,
                    'luggage'          => $luggage,
                    'job_type'         => $jobType,
                    'vehicle_type'     => $vehicleType,
                    'special_notes'    => $specialNotes,
                    'customer_details' => $customerDetails,
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch job details: ' . $e->getMessage()
            ], 500);
        }
    }
}
