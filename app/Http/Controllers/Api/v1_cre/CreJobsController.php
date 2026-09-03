<?php

namespace App\Http\Controllers\Api\v1_cre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CreJobsController extends Controller
{
    public $serviceAccountPath;
    public $serviceAccount;

    public function __construct()
    {
        $this->serviceAccountPath = storage_path('app/firebase/firebase-config.json');

        if (file_exists($this->serviceAccountPath)) {
            $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        }
    }

    public function getAccessToken()
    {
        if (empty($this->serviceAccount)) {
            return null;
        }

        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));

        $now = time();
        $claimSet = [
            'iss'   => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => $this->serviceAccount['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600
        ];

        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput  = "$header.$claimSetEncoded";

        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount['private_key']),
            OPENSSL_ALGO_SHA256
        );

        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);

        $ch = curl_init($this->serviceAccount['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }

    public function getFcm($id = null, $loc = null)
    {
        if ($id) {
            $get_tokens = DB::table('user_register')
                ->whereIn('id', $id)
                ->where('deletes', '0')
                ->where('notify', 1)
                ->get();

            $tokens = [];
            foreach ($get_tokens as $user) {
                if (!empty($user->fcm_token)) {
                    $tokens[] = $user->fcm_token;
                }
                if (!empty($user->browser_fcm_token)) {
                    $tokens[] = $user->browser_fcm_token;
                }
            }
            return $tokens;
        }

        return [];
    }

    public function sendFCM($accessToken, $fcmToken, $title, $body, $data = [])
    {
        $stringData = [];
        foreach ($data as $key => $value) {
            $validKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
            $stringData[$validKey] = (string) $value;
        }

        $stringData['title'] = $title;
        $stringData['body']  = $body;

        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->serviceAccount['project_id'] . '/messages:send';

        $payload = [
            'validate_only' => false,
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'ttl'      => '86400s',
                    'notification' => [
                        'channel_id' => 'new_job_channel',
                        'sound'      => 'custom_notification',
                        'color'      => '#FF6B35',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority'  => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'sound' => 'custom_notification.wav',
                            'badge' => 1
                        ]
                    ]
                ],
                'data' => $stringData
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function parseFirestoreFields(array $fields): array
    {
        $result = [];
        foreach ($fields as $key => $value) {
            if (isset($value['stringValue'])) {
                $result[$key] = $value['stringValue'];
            } elseif (isset($value['integerValue'])) {
                $result[$key] = (int) $value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $result[$key] = (float) $value['doubleValue'];
            } elseif (isset($value['booleanValue'])) {
                $result[$key] = (bool) $value['booleanValue'];
            } elseif (isset($value['timestampValue'])) {
                $result[$key] = Carbon::parse(
                    $value['timestampValue'],
                    'UTC'
                )->setTimezone(config('app.timezone'))->toDateTimeString();
            } elseif (isset($value['mapValue']['fields'])) {
                $result[$key] = $this->parseFirestoreFields(
                    $value['mapValue']['fields']
                );
            } elseif (isset($value['arrayValue']['values'])) {
                $result[$key] = array_map(function ($v) {
                    return $this->parseFirestoreFields([$v])[0] ?? null;
                }, $value['arrayValue']['values']);
            } else {
                $result[$key] = null;
            }
        }
        return $result;
    }

    public function sendCancelWhatsAppMessage($cleanPhone, $templateName, $template, $parameters, $url, $request = null)
    {
        $bodyParameters = [];
        foreach ($parameters as $param) {
            $val = ($param !== null && $param !== '') ? (string) $param : 'Not Specified';
            $bodyParameters[] = [
                "type" => "text",
                "text" => $val
            ];
        }

        $components = [];

        if ($template && !empty($template->header_image)) {
            $components[] = [
                "type" => "header",
                "parameters" => [
                    [
                        "type"  => "image",
                        "image" => [
                            "link" => $template->header_image
                        ]
                    ]
                ]
            ];
        }

        if (!empty($bodyParameters)) {
            $components[] = [
                "type"       => "body",
                "parameters" => $bodyParameters
            ];
        }

        if ($template && !empty($template->variables_json)) {
            $buttonsConfig = json_decode($template->variables_json, true);
            if (!empty($buttonsConfig['buttons'])) {
                foreach ($buttonsConfig['buttons'] as $index => $btn) {
                    if ($btn['type'] === 'COPY_CODE') {
                        $components[] = [
                            "type"       => "button",
                            "sub_type"   => "url",
                            "index"      => (string)$index,
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string)($parameters[0] ?? '123456')
                                ]
                            ]
                        ];
                    }
                    if ($btn['type'] === 'URL' && strpos($btn['url'] ?? '', '{{1}}') !== false) {
                        $components[] = [
                            "type"       => "button",
                            "sub_type"   => "url",
                            "index"      => (string)$index,
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string)($parameters[0] ?? '')
                                ]
                            ]
                        ];
                    }
                }
            }
        }

        $templatePayload = [
            "name"     => $templateName,
            "language" => [
                "code" => "en_US"
            ]
        ];

        if (!empty($components)) {
            $templatePayload["components"] = $components;
        }

        $payload = [
            "messaging_product" => "whatsapp",
            "to"                => $cleanPhone,
            "type"              => "template",
            "template"          => $templatePayload
        ];

        $reqTime = now();
        Log::info('CRE WhatsApp Request Payload:', ['to' => $cleanPhone, 'payload' => $payload]);

        $token = env('FB_WHATSAPP_TOKEN');
        $response = Http::withToken($token)->acceptJson()->post($url, $payload);

        $resTime = now();
        $body = $response->json();
        $isSuccess = $response->successful();
        $messageId = $body['messages'][0]['id'] ?? null;

        Log::info('CRE WhatsApp Response:', ['status' => $response->status(), 'response' => $body]);

        try {
            DB::table('smslog')->insert([
                'gateway'        => 'fbWhatsapp',
                'subject'        => 'Job Cancelled by CRE',
                'details'        => json_encode($parameters),
                'mobile'         => $cleanPhone,
                'ip'             => ($request && method_exists($request, 'ip')) ? $request->ip() : '',
                'datetime'       => now(),
                'token_response' => json_encode($body),
                'status'         => $isSuccess ? 'sent' : 'failed',
                'smsstatus'      => $isSuccess ? 'Sent' : 'Failed',
                'reference_id'   => $messageId ?? '',
                'site'           => 'CUSTOMER',
                'REQ_Time'       => $reqTime,
                'RES_Time'       => $resTime,
                'smsdetails'     => json_encode($payload),
            ]);
        } catch (\Throwable $se) {
            Log::error('CRE smslog insert error: ' . $se->getMessage());
        }
    }

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

            $now = now();

            $rawJobs = DB::table('cus_job_temp')
                ->select([
                    'id',
                    'job_no',
                    'global_type',
                    'user_id',
                    'user_details',
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
                ->where(function ($q) use ($now) {
                    $q->whereNull('pickup_date')
                      ->orWhere('pickup_date', '>=', $now);
                })
                ->orderBy('id', 'desc')
                ->get();

            $unassignedJobs = [];

            foreach ($rawJobs as $job) {
                if (!empty($job->pickup_date)) {
                    try {
                        if (Carbon::parse($job->pickup_date)->isPast()) {
                            continue;
                        }
                    } catch (\Throwable $e) {
                    }
                }
                $jobNo = $job->job_no ?? ('GR-' . $job->id);

                $userId = (int) ($job->user_id ?? 0);
                $hasUserDetails = !empty($job->user_details);

                $source = "From Website";
                if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$job->global_type) === 'customer') {
                    $source = "Customer App Job";
                } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$job->global_type) === 'driver') {
                    $source = "From Driver App";
                } elseif ($userId == 0 || $hasUserDetails || strtolower((string)$job->global_type) === 'website') {
                    $source = "From Website";
                }

                $badge = "New";
                if (strtolower((string)$job->global_type) === 'schedule' || strtolower((string)$job->job_status) === 'schedule') {
                    $badge = "Schedule";
                }

                $from = !empty($job->pick_address) ? $job->pick_address : ($job->from_place ?? '');
                $to   = !empty($job->drop_address) ? $job->drop_address : ($job->to_place ?? '');

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
            $jobId = $request->input('job_id');

            if (!$jobId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job ID is required'
                ], 422);
            }

            $job = DB::table('cus_job_temp')
                ->where('id', $jobId)
                ->where('deletes', '0')
                ->first();

            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found'
                ], 404);
            }

            $jobNo = $job->job_no ?? ('GR-' . $job->id);

            $userId = (int) ($job->user_id ?? 0);
            $hasUserDetails = !empty($job->user_details);

            $details = [];
            if (!empty($job->user_details)) {
                $details = is_string($job->user_details) ? json_decode($job->user_details, true) : (array)$job->user_details;
            }
            if (!is_array($details)) $details = [];

            $source = "From Website";
            if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$job->global_type) === 'customer') {
                $source = "Customer App Job";
            } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$job->global_type) === 'driver') {
                $source = "From Driver App";
            } elseif ($userId == 0 || $hasUserDetails || strtolower((string)$job->global_type) === 'website') {
                $source = "From Website";
            }

            $badge = "New";
            if (strtolower((string)$job->global_type) === 'schedule' || strtolower((string)$job->job_status) === 'schedule') {
                $badge = "Schedule";
            }

            $from = !empty($job->pick_address) ? $job->pick_address : ($job->from_place ?? '');
            $to   = !empty($job->drop_address) ? $job->drop_address : ($job->to_place ?? '');

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

            $rawPass = $job->pass_count ?? ($details['pass_count'] ?? 1);
            $passClean = strtolower(trim((string)$rawPass));
            if ($passClean === 'mini' || $passClean === '5 mini') {
                $passengers = "5 Passengers";
            } elseif (is_numeric($rawPass)) {
                $count = (int) $rawPass;
                $passengers = $count . ($count == 1 ? ' Passenger' : ' Passengers');
            } else {
                $passengers = (string) $rawPass;
            }

            $luggCount = (int) ($job->lugg_count ?? ($details['lugg_count'] ?? ($details['luggage'] ?? 0)));
            $luggage   = $luggCount . ($luggCount == 1 ? ' Luggage' : ' Luggage');

            $explicitVehicleType = null;
            if (!empty($details['cab_type'])) {
                $explicitVehicleType = $details['cab_type'];
            } elseif (!empty($details['car_type'])) {
                $explicitVehicleType = $details['car_type'];
            } elseif (!empty($job->car_type)) {
                $explicitVehicleType = $job->car_type;
            } elseif (!empty($job->cab_type)) {
                $explicitVehicleType = $job->cab_type;
            }

            if ($source === "From Website" && !empty($hasUserDetails) && !empty($explicitVehicleType)) {
                $vehicleType = $explicitVehicleType;
            } else {
                if ($passClean === '5 mini' || $passClean === 'mini') {
                    $vehicleType = "Go Mini";
                } elseif ($passClean === '8' || (int)$rawPass === 8) {
                    $vehicleType = "Go 7Seater";
                } elseif ($passClean === '7' || (int)$rawPass === 7) {
                    $vehicleType = "Go 6Seater";
                } elseif ($passClean === '5' || (int)$rawPass === 5) {
                    $vehicleType = "Go 4Seater";
                } elseif ($passClean === '4' || (int)$rawPass === 4) {
                    $vehicleType = "Go 4Seater";
                } elseif ($passClean === '6' || (int)$rawPass === 6) {
                    $vehicleType = "Go 6Seater";
                } else {
                    $vehicleType = $explicitVehicleType ?? "Go 4Seater";
                }
            }

            $rawJobType = $details['job_type'] ?? ($details['trip_type'] ?? ($job->job_type ?? 'One Way'));
            $cleanType  = strtolower(trim((string)$rawJobType));
            if ($cleanType === 'oneway') {
                $jobType = "One Way";
            } elseif ($cleanType === 'roundtrip') {
                $jobType = "Round Trip";
            } else {
                $jobType = ucfirst((string)$rawJobType);
            }

            $specialNotes = !empty($job->job_remark) ? $job->job_remark : ($job->comments ?? ($details['special_notes'] ?? ($details['notes'] ?? '')));

            $distance = $job->distance ?? ($details['distance'] ?? '');
            $duration = $job->duration ?? ($details['duration'] ?? '');
            $day = $job->day ?? ($details['day'] ?? '');

            // Coordinate extraction based on image_5ff39f.png
            $pickLat = $job->pick_lat ?? ($details['pick_lat'] ?? '');
            $pickLan = $job->pick_lan ?? ($details['pick_lan'] ?? '');
            $dropLat = $job->drop_lat ?? ($details['drop_lat'] ?? '');
            $dropLan = $job->drop_lan ?? ($details['drop_lan'] ?? '');

            $customerName   = '';
            $customerMobile = '';
            $profileImg     = null;

            if ($source === "From Website" || $userId == 0 || !empty($details)) {
                if (!empty($details)) {
                    $customerName   = $details['name'] ?? ($details['customer_name'] ?? ($details['user_name'] ?? ''));
                    $customerMobile = $details['mobile'] ?? ($details['phone'] ?? ($details['call_number'] ?? ''));
                }
                if (empty($customerName) && $userId > 0) {
                    $customer = DB::table('customer_register')->where('id', $userId)->first();
                    if ($customer) {
                        $customerName   = $customer->name ?? '';
                        $customerMobile = $customer->mobile ?? '';
                        $profileImg     = $customer->img_url ?? null;
                    }
                }
                if (empty($customerName)) {
                    $customerName = 'Website Customer';
                }
            } else {
                if ($userId > 0) {
                    $customer = DB::table('customer_register')->where('id', $userId)->first();
                    if ($customer) {
                        $customerName   = $customer->name ?? '';
                        $customerMobile = $customer->mobile ?? '';
                        $profileImg     = $customer->img_url ?? null;
                    }
                }
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

            // Build base response data
            $responseData = [
                'job_id'           => $job->id,
                'job_no'           => $jobNo,
                'badge'            => $badge,
                'source'           => $source,
                'from'             => $from,
                'to'               => $to,
                'pick_lat'         => (string) $pickLat,
                'pick_lan'         => (string) $pickLan,
                'drop_lat'         => (string) $dropLat,
                'drop_lan'         => (string) $dropLan,
                'distance'         => (string) $distance,
                'duration'         => (string) $duration,
            ];

            // Only conditionally include 'days' if it is a Round Trip
            if ($jobType === 'Round Trip' || $cleanType === 'round trip') {
                $responseData['days'] = (string) $day;
            }

            // Append the remaining details
            $responseData = array_merge($responseData, [
                'date'             => $formattedDate,
                'time'             => $formattedTime,
                'passengers'       => $passengers,
                'luggage'          => $luggage,
                'job_type'         => $jobType,
                'vehicle_type'     => $vehicleType,
                'special_notes'    => $specialNotes,
                'customer_details' => $customerDetails,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Job details retrieved successfully',
                'data'    => $responseData
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch job details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelJob(Request $request)
    {
        try {
            $jobId = $request->input('job_id');

            if (!$jobId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job ID is required'
                ], 422);
            }

            $creUser = $request->get('cre_user') ?? auth()->user();
            $cancelledBy = $creUser->id ?? auth()->id() ?? 0;
            $cancelReason = $request->input('cancel_reason') ?? 'Cancelled by CRE';

            $get_job = DB::table('cus_job_temp')
                ->where('id', $jobId)
                ->where('deletes', '0')
                ->first();

            if (!$get_job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job Not Found'
                ], 404);
            }

            if (strtolower((string) $get_job->job_status) === 'cancelled') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job is already cancelled'
                ], 400);
            }

            DB::table('cus_job_temp')
                ->where('id', $get_job->id)
                ->update([
                    'job_status'     => 'cancelled',
                    'confirm_status' => 0
                ]);

            DB::table('open_jobs')
                ->where('job_no', $get_job->job_no)
                ->orWhere('id', $get_job->id)
                ->update([
                    'job_status'     => 'cancelled',
                    'confirm_status' => 0
                ]);

            DB::table('job_cancellations')->insert([
                'job_id'       => $get_job->id,
                'customer_id'  => $get_job->user_id,
                'cancelled_by' => $cancelledBy,
                'reason'       => $cancelReason,
                'created_at'   => Carbon::now()
            ]);

            $get_bidders_ids = [];

            if (!empty($this->serviceAccount) && !empty($get_job->job_no)) {
                try {
                    $firebase = new \App\Services\FirebaseJobService(
                        $this->serviceAccount['project_id'],
                        $this->getAccessToken()
                    );

                    $jobDoc = $firebase->getJob($get_job->job_no);
                    $jobData = $jobDoc ? $this->parseFirestoreFields($jobDoc) : [];
                    $get_bidders_ids = array_keys($jobData['bids_details'] ?? []);

                    $firebase->deleteJob($get_job->job_no);
                } catch (\Throwable $fe) {
                    Log::error('CRE Cancel Job Firebase Error: ' . $fe->getMessage());
                }
            }

            $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";
            $templateName = 'admin_cancle_jobs';
            $template = DB::table('wamail_templates')->where('name', $templateName)->first();

            $customerName = 'Customer';
            $customerMobile = '';

            $userId = (int) ($get_job->user_id ?? 0);

            if ($userId > 0) {
                $get_u = DB::table('customer_register')->where('id', $userId)->where('deletes', 0)->first();
                if (!$get_u) {
                    $get_u = DB::table('user_register')->where('id', $userId)->where('deletes', '0')->first();
                }

                if ($get_u) {
                    $customerName = $get_u->name ?? 'Customer';
                    $customerMobile = $get_u->mobile ?? '';
                }
            }

            if (empty($customerMobile) && !empty($get_job->user_details)) {
                $userDetails = is_string($get_job->user_details) ? json_decode($get_job->user_details, true) : (array) $get_job->user_details;
                if (is_array($userDetails)) {
                    if (empty($customerName) || $customerName === 'Customer') {
                        $customerName = $userDetails['name'] ?? ($userDetails['customer_name'] ?? 'Customer');
                    }
                    $customerMobile = $userDetails['mobile'] ?? ($userDetails['phone'] ?? ($userDetails['call_number'] ?? ''));
                }
            }

            Log::info('CRE Cancel Job Customer Resolved:', [
                'user_id' => $get_job->user_id,
                'customerName' => $customerName,
                'customerMobile' => $customerMobile,
                'template_found' => !empty($template)
            ]);

            if (!empty($customerMobile)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', (string)$customerMobile);
                if (strlen($cleanPhone) === 10) {
                    $cleanPhone = '91' . $cleanPhone;
                }

                if (!empty($cleanPhone)) {
                    $jobNo = $get_job->job_no ?? ('GR-' . $get_job->id);
                    $pickupLoc = !empty($get_job->pick_address) ? $get_job->pick_address : (!empty($get_job->from_place) ? $get_job->from_place : 'Not Specified');
                    $dropLoc   = !empty($get_job->drop_address) ? $get_job->drop_address : (!empty($get_job->to_place) ? $get_job->to_place : 'Not Specified');
                    $rawDate   = $get_job->pickup_date ?? $get_job->day ?? $get_job->created_at ?? null;
                    $formattedDate = !empty($rawDate) ? Carbon::parse($rawDate)->format('d-m-Y h:i A') : 'Not Specified';

                    $parameters = [$customerName, $jobNo, $pickupLoc, $dropLoc, $formattedDate];

                    Log::info('CRE Cancel Job sending WhatsApp:', [
                        'cleanPhone' => $cleanPhone,
                        'parameters' => $parameters
                    ]);

                    try {
                        $this->sendCancelWhatsAppMessage($cleanPhone, $templateName, $template, $parameters, $url, $request);
                    } catch (\Throwable $we) {
                        Log::error('CRE WhatsApp Customer Exception: ' . $we->getMessage());
                    }
                } else {
                    Log::warning('CRE Cancel Job cleanPhone is empty for mobile: ' . $customerMobile);
                }
            } else {
                Log::warning('CRE Cancel Job customerMobile is empty for job id: ' . $get_job->id);
            }

            if (count($get_bidders_ids) > 0) {
                $accessToken = $this->getAccessToken();
                $fcmTokens   = $this->getFcm($get_bidders_ids);

                if ($fcmTokens && count($fcmTokens) && $accessToken) {
                    foreach ($fcmTokens as $token) {
                        try {
                            $this->sendFCM(
                                $accessToken,
                                $token,
                                'Your Bid Has Been Cancelled',
                                'Job ID ' . ($get_job->job_no ?? ('GR-' . $get_job->id)) . ': Unfortunately, the job has been cancelled.',
                                [
                                    'caller' => 'CRE',
                                    'type'   => 'cancel_notification',
                                    'url'    => env('APP_URL') . 'jobs',
                                ]
                            );
                        } catch (\Throwable $e) {
                            Log::error('CRE FCM send error for token: ' . $token, [
                                'message' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Job cancelled successfully.'
            ], 200);

        } catch (\Throwable $e) {
            Log::error('CRE cancelJob Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Failed to cancel job: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDistrictList(Request $request)
    {
        try {
            $districts = DB::table('districts')
                ->select(['id', 'district_name'])
                ->get();

            return response()->json([
                'status'  => true,
                'message' => 'Districts list retrieved successfully',
                'data'    => $districts
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch district list: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendJobNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_id'      => 'required',
                'title'       => 'nullable|string|max:255',
                'body'        => 'nullable|string',
                'district_id' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $jobIdIn = $request->input('job_id');
            $districtId = (int) ($request->input('district_id') ?? 0);

            $job = DB::table('cus_job_temp')
                ->where('deletes', '0')
                ->where(function ($q) use ($jobIdIn) {
                    $q->where('id', $jobIdIn)
                      ->orWhere('job_no', $jobIdIn);
                })
                ->first();

            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found.'
                ], 404);
            }

            $jobId = $job->id;
            $jobNo = $job->job_no ?? ('GR-' . $job->id);

            $title = $request->input('title') ?? 'New Job Available';
            $body  = $request->input('body') ?? ('New job ' . $jobNo . ' is available. Open the app to bid/accept.');

            $creUser = $request->get('cre_user') ?? auth()->user();
            $creUserId = $creUser->id ?? auth()->id() ?? 0;
            $controller = $this;

            $reqJsonData = json_encode([
                'job_id'      => $jobId,
                'job_no'      => $jobNo,
                'target'      => 'drivers',
                'district_id' => $districtId
            ]);

            $existingLog = DB::table('push_notifications')
                ->where(function($q) use ($jobNo, $jobId) {
                    $q->where('req_json', 'LIKE', '%"job_id":' . $jobId . '%')
                      ->orWhere('req_json', 'LIKE', '%"job_id":"' . $jobId . '"%')
                      ->orWhere('req_json', 'LIKE', '%"job_no":"' . $jobNo . '"%');
                })
                ->where('req_json', 'LIKE', '%"target":"drivers"%')
                ->where('deletes', '0')
                ->orderBy('id', 'desc')
                ->first();

            $trackingId = null;
            $existingDelivered = [];
            $existingFailed = [];
            $excludedDriverIds = [];

            if ($existingLog) {
                $trackingId = $existingLog->id;
                if (!empty($existingLog->res_json)) {
                    $resData = json_decode($existingLog->res_json, true);
                    if (is_array($resData)) {
                        $existingDelivered = $resData['delivered'] ?? [];
                        $existingFailed    = $resData['not_delivered'] ?? [];
                        foreach ($existingDelivered as $d) {
                            if (isset($d['id'])) $excludedDriverIds[] = (int) $d['id'];
                        }
                        foreach ($existingFailed as $f) {
                            if (isset($f['id'])) $excludedDriverIds[] = (int) $f['id'];
                        }
                    }
                }

                DB::table('push_notifications')->where('id', $trackingId)->update([
                    'sent_by'    => $creUserId,
                    'title'      => $title,
                    'body'       => $body,
                    'status'     => 2,
                    'req_json'   => $reqJsonData,
                    'updated_at' => now(),
                ]);
            } else {
                $trackingId = DB::table('push_notifications')->insertGetId([
                    'user_id'    => '0',
                    'sent_by'    => $creUserId,
                    'title'      => $title,
                    'body'       => $body,
                    'status'     => 2,
                    'req_json'   => $reqJsonData,
                    'res_json'   => json_encode(['status' => 'Processing in background...', 'job_no' => $jobNo]),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deletes'    => 0,
                ]);
            }

            app()->terminating(function () use ($controller, $title, $body, $jobId, $jobNo, $trackingId, $districtId, $excludedDriverIds, $existingDelivered, $existingFailed) {
                try {
                    set_time_limit(0);
                    ini_set('memory_limit', '512M');

                    $accessToken = $controller->getAccessToken();
                    if (!$accessToken) {
                        DB::table('push_notifications')->where('id', $trackingId)->update([
                            'status'     => 0,
                            'res_json'   => json_encode(['error' => 'Failed to obtain FCM access token']),
                            'updated_at' => now(),
                        ]);
                        return;
                    }

                    $newDeliveredList = [];
                    $newNotDeliveredList = [];

                    $query = DB::table('user_register')
                        ->where(function($q) {
                            $q->where('deletes', '0')->orWhere('deletes', 0);
                        })
                        ->whereNotNull('fcm_token')
                        ->where('fcm_token', '!=', '');

                    if ($districtId > 0) {
                        $query->where('districts_id', $districtId);
                    }

                    if (!empty($excludedDriverIds)) {
                        $query->whereNotIn('id', $excludedDriverIds);
                    }

                    $query->select('id', 'name', 'fcm_token')
                          ->orderBy('id', 'asc');

                    $query->chunk(500, function ($drivers) use ($controller, $accessToken, $title, $body, $jobId, $jobNo, &$newDeliveredList, &$newNotDeliveredList) {
                        foreach ($drivers as $user) {
                            $uName = !empty($user->name) ? $user->name : 'Driver';
                            $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                            $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                            try {
                                $response = $controller->sendFCM($accessToken, $user->fcm_token, $personalizedTitle, $personalizedBody, [
                                    'type'   => 'admin_broadcast_district',
                                    'job_id' => $jobId,
                                    'job_no' => $jobNo
                                ]);

                                if (isset($response['name'])) {
                                    $newDeliveredList[] = [
                                        'id'   => (int) $user->id,
                                        'name' => $uName
                                    ];
                                } else {
                                    $newNotDeliveredList[] = [
                                        'id'    => (int) $user->id,
                                        'name'  => $uName,
                                        'error' => 'FCM Response Error'
                                    ];
                                }
                            } catch (\Throwable $e) {
                                $newNotDeliveredList[] = [
                                    'id'    => (int) $user->id,
                                    'name'  => $uName,
                                    'error' => 'FCM Response Error'
                                ];
                            }
                        }
                    });

                    $finalDelivered = array_values(array_merge($existingDelivered, $newDeliveredList));
                    $finalFailed    = array_values(array_merge($existingFailed, $newNotDeliveredList));

                    $resJsonData = [
                        'success_count' => count($finalDelivered),
                        'failure_count' => count($finalFailed),
                        'delivered'     => $finalDelivered,
                        'not_delivered' => $finalFailed
                    ];

                    DB::table('push_notifications')->where('id', $trackingId)->update([
                        'status'     => 1,
                        'res_json'   => json_encode($resJsonData, JSON_UNESCAPED_UNICODE),
                        'updated_at' => now(),
                    ]);

                } catch (\Throwable $exception) {
                    Log::error('CRE sendJobNotification terminating error: ' . $exception->getMessage());
                    DB::table('push_notifications')->where('id', $trackingId)->update([
                        'status'     => 0,
                        'res_json'   => json_encode(['error' => 'Process failed: ' . $exception->getMessage()]),
                        'updated_at' => now(),
                    ]);
                }
            });

            return response()->json([
                'status'      => true,
                'message'     => 'Notifications to drivers sent to queue!',
                'tracking_id' => $trackingId
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to send job notification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAssignedJobList(Request $request)
    {
        try {
            $startDate  = trim((string) ($request->input('startDate') ?? $request->input('start_date') ?? ''));
            $endDate    = trim((string) ($request->input('endDate') ?? $request->input('end_date') ?? ''));
            $filterType = trim((string) ($request->input('filterType') ?? $request->input('filter_type') ?? 'pickup'));

            $currentDateTime = now()->toDateTimeString();
            $dateCol = ($filterType === 'created') ? 'c.created_at' : 'c.pickup_date';

            $query = DB::table('cus_job_temp as c')
                ->select([
                    'c.id',
                    'c.job_no',
                    'c.job_type',
                    'c.job_status',
                    'c.global_type',
                    'c.user_id',
                    'c.assigned_to',
                    'c.pick_address',
                    'c.drop_address',
                    'c.from_place',
                    'c.to_place',
                    'c.pickup_date',
                    'c.created_at',
                    'c.pass_count',
                    'c.fare_breakdown',
                    'c.fare',
                    'c.user_details',
                    'c.preview_hash',
                    'd.name as driver_name',
                    'd.mobile as driver_mobile'
                ])
                ->leftJoin('user_register as d', 'c.assigned_to', '=', 'd.id')
                ->where('c.deletes', '0')
                ->where(function ($q) {
                    $q->whereNull('c.job_no')
                      ->orWhere('c.job_no', 'NOT LIKE', 'GRP-%');
                });

            // Filter ONLY assigned status jobs (omit started, cancelled, dropped, completed, etc.)
            $query->where(function ($q) {
                $q->whereIn('c.job_status', ['accept', 'accepted', 'assigned'])
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('c.assigned_to')
                         ->where('c.assigned_to', '>', 0)
                         ->whereNotIn('c.job_status', ['started', 'cancelled', 'dropped', 'completed']);
                  });
            });

            // Apply Date Filter or Default (assigned pickup date must not exceed current time)
            if (!empty($startDate) && !empty($endDate)) {
                $start = Carbon::parse($startDate)->startOfDay()->toDateTimeString();
                $end   = Carbon::parse($endDate)->endOfDay()->toDateTimeString();
                $query->whereBetween($dateCol, [$start, $end]);
            } else {
                // Default: Assigned status jobs where pickup date does not exceed current time
                $query->where('c.pickup_date', '<=', $currentDateTime);
            }

            $jobs = $query->orderBy('c.pickup_date', 'desc')->get();

            if ($jobs->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'data'   => []
                ], 200);
            }

            $jobIds      = [];
            $customerIds = [];
            $driverIds   = [];
            $pushJobIds  = [];

            foreach ($jobs as $row) {
                $jobIds[] = $row->id;
                $uid   = (int) ($row->user_id ?? 0);
                $hasUserDetails = !empty($row->user_details);
                $gType = 'website';
                $jobNo = $row->job_no ?? '';

                if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$row->global_type) === 'customer') {
                    $gType = 'customer';
                } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$row->global_type) === 'driver') {
                    $gType = 'driver';
                } elseif (isset($row->global_type) && $row->global_type === 'schedule') {
                    $gType = 'schedule';
                } elseif ($uid == 0 || $hasUserDetails) {
                    $gType = 'website';
                }

                $row->calc_global_type = $gType;

                if (!empty($uid) && $uid != 0 && $gType !== 'website') {
                    if ($gType === 'driver') {
                        $driverIds[$uid] = $uid;
                    } else {
                        $customerIds[$uid] = $uid;
                    }
                }

                if (!empty($row->assigned_to)) {
                    $driverIds[$row->assigned_to] = $row->assigned_to;
                }

                $pushJobIds[$row->id] = $row->id;
            }

            // Fetch Ratings for fetched jobs in batch
            $ratings = [];
            if (!empty($jobIds)) {
                $fbRows = DB::table('customer_feedback')
                    ->whereIn('job_id', $jobIds)
                    ->select('job_id', 'rating')
                    ->get();
                foreach ($fbRows as $fb) {
                    $ratings[$fb->job_id] = $fb->rating;
                }
            }

            // Fetch Customer Data in batch
            $customerData = [];
            if (!empty($customerIds)) {
                $custs = DB::table('customer_register')
                    ->whereIn('id', array_keys($customerIds))
                    ->select(['id', 'name', 'mobile', 'email'])
                    ->get();
                foreach ($custs as $c) {
                    $customerData[$c->id] = (array)$c;
                }
            }

            // Fetch Driver Data in batch
            $driverData = [];
            if (!empty($driverIds)) {
                $drvs = DB::table('user_register')
                    ->whereIn('id', array_keys($driverIds))
                    ->select(['id', 'name', 'mobile', 'email', 'vehicle_details'])
                    ->get();
                foreach ($drvs as $d) {
                    $vType = null;
                    if (!empty($d->vehicle_details)) {
                        $vDetails = is_string($d->vehicle_details) ? json_decode($d->vehicle_details, true) : (array)$d->vehicle_details;
                        $vType = $vDetails['type'] ?? ($vDetails['vehicle_type'] ?? null);
                    }
                    $driverData[$d->id] = [
                        'id'              => $d->id,
                        'name'            => $d->name,
                        'mobile'          => $d->mobile,
                        'email'           => $d->email,
                        'cab_type'        => $vType ?? 'Standard',
                        'vehicle_details' => $d->vehicle_details
                    ];
                }
            }

            // Fetch Driver Locations (latest location per driver)
            $driverLocations = [];
            if (!empty($driverIds)) {
                $locs = DB::table('drivers_current_location')
                    ->whereIn('user_id', array_keys($driverIds))
                    ->get();

                foreach ($locs as $loc) {
                    $driverLocations[$loc->user_id] = [
                        'lat'              => $loc->lat ?? null,
                        'lng'              => $loc->lng ?? null,
                        'current_state'    => $loc->current_state ?? '',
                        'current_district' => $loc->current_district ?? '',
                        'current_address'  => $loc->current_address ?? '',
                        'updated_at'       => $loc->updated_at ?? null,
                    ];
                }
            }

            // Fast Push Notification Count Lookup
            $pushData = [];
            if (!empty($pushJobIds)) {
                $pushLogs = DB::table('push_notifications')
                    ->select(['req_json', 'res_json'])
                    ->where('deletes', '0')
                    ->where('req_json', 'LIKE', '%"target":"drivers"%')
                    ->orderBy('id', 'desc')
                    ->limit(200)
                    ->get();

                foreach ($pushLogs as $pLog) {
                    if (!empty($pLog->res_json) && !empty($pLog->req_json)) {
                        $reqArr = json_decode($pLog->req_json, true);
                        $resArr = json_decode($pLog->res_json, true);
                        $jid    = $reqArr['job_id'] ?? null;
                        if ($jid && isset($pushJobIds[$jid]) && isset($resArr['success_count'])) {
                            $pushData[$jid] = (int)$resArr['success_count'];
                        }
                    }
                }
            }

            $finalJobs = [];
            foreach ($jobs as $rawRow) {
                $jobNo = $rawRow->job_no ?? ('GR-' . $rawRow->id);
                $gType = $rawRow->calc_global_type ?? 'website';

                $source = "From Website";
                if ($gType === 'customer') {
                    $source = "Customer App Job";
                } elseif ($gType === 'driver') {
                    $source = "From Driver App";
                } else {
                    $source = "From Website";
                }

                $from = !empty($rawRow->pick_address) ? $rawRow->pick_address : ($rawRow->from_place ?? '');
                $to   = !empty($rawRow->drop_address) ? $rawRow->drop_address : ($rawRow->to_place ?? '');

                $dateStr = $rawRow->pickup_date ?? $rawRow->created_at ?? null;
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

                $rawPass = $rawRow->pass_count ?? 1;
                if (is_string($rawPass) && strtolower(trim($rawPass)) === 'mini') {
                    $passengers = "5 Passengers";
                    $passCount = 5;
                } elseif (is_numeric($rawPass)) {
                    $count = (int) $rawPass;
                    $passengers = $count . ($count == 1 ? ' Passenger' : ' Passengers');
                    $passCount = $count;
                } else {
                    $passengers = (string) $rawPass;
                    $passCount = 5;
                }

                $totalFare = 0;
                $commission = 0;
                $tax = 0;
                $baseFare = (float)($rawRow->fare ?? 0);

                if (!empty($rawRow->fare_breakdown) && is_string($rawRow->fare_breakdown)) {
                    $fareData = json_decode($rawRow->fare_breakdown, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($fareData)) {
                        $totalFare  = isset($fareData['total_fare']) ? (float)$fareData['total_fare'] : ((float)($rawRow->fare ?? 0));
                        $commission = isset($fareData['com']) ? (float)$fareData['com'] : 0;
                        $tax        = isset($fareData['tax']) ? (float)$fareData['tax'] : (isset($fareData['tax_fare']) ? (float)$fareData['tax_fare'] : 0);
                        if (isset($fareData['base_fare'])) {
                            $baseFare = (float)$fareData['base_fare'];
                        }
                    }
                }

                if ($totalFare <= 0) {
                    $totalFare = (float)($rawRow->fare ?? 0);
                }

                $driverEarned = $totalFare - $commission - $tax;
                if ($driverEarned < 0) {
                    $driverEarned = 0;
                }
                if ($baseFare <= 0) {
                    $baseFare = $driverEarned > 0 ? $driverEarned : $totalFare;
                }

                $uid = $rawRow->user_id ?? 0;
                $posterName = 'Customer';
                $mobile = '';
                $custEmail = '';

                if ($gType === 'website') {
                    if (!empty($rawRow->user_details)) {
                        $uDetails = is_string($rawRow->user_details) ? json_decode($rawRow->user_details, true) : (array)$rawRow->user_details;
                        $posterName = $uDetails['name'] ?? ($uDetails['customer_name'] ?? ($uDetails['user_name'] ?? ''));
                        $mobile     = $uDetails['mobile'] ?? ($uDetails['phone'] ?? ($uDetails['call_number'] ?? ''));
                        $custEmail  = $uDetails['email'] ?? '';
                    }
                    if (empty($posterName) && !empty($uid) && isset($customerData[$uid])) {
                        $posterName = $customerData[$uid]['name'] ?? '';
                        $mobile     = $customerData[$uid]['mobile'] ?? $mobile;
                        $custEmail  = $customerData[$uid]['email'] ?? $custEmail;
                    }
                    if (empty($posterName)) {
                        $posterName = 'Website Customer';
                    }
                } else {
                    if (!empty($uid) && $uid != 0) {
                        if ($gType === 'driver' && isset($driverData[$uid])) {
                            $posterName = $driverData[$uid]['name'] ?? 'Driver Customer';
                            $mobile     = $driverData[$uid]['mobile'] ?? '';
                            $custEmail  = $driverData[$uid]['email'] ?? '';
                        } elseif (isset($customerData[$uid])) {
                            $posterName = $customerData[$uid]['name'] ?? 'Customer';
                            $mobile     = $customerData[$uid]['mobile'] ?? '';
                            $custEmail  = $customerData[$uid]['email'] ?? '';
                        }
                    }
                }

                $assignedTo = $rawRow->assigned_to ?? 0;
                $driverLoc  = isset($driverLocations[$assignedTo]) ? $driverLocations[$assignedTo] : null;

                $jobStatus   = strtolower($rawRow->job_status ?? '');
                $statusLabel = 'Assigned';
                if ($jobStatus === 'started') {
                    $statusLabel = 'Started';
                } elseif ($jobStatus === 'completed') {
                    $statusLabel = 'Completed';
                } elseif ($jobStatus === 'cancelled') {
                    $statusLabel = 'Cancelled';
                } elseif (in_array($jobStatus, ['accept', 'accepted']) && !empty($rawRow->pickup_date) && Carbon::parse($rawRow->pickup_date)->isPast()) {
                    $statusLabel = 'Incomplete';
                }

                $previewHash = $rawRow->preview_hash ?? null;

                $finalJobs[] = [
                    'job_id'             => $rawRow->id,
                    'job_no'             => $jobNo,
                    'job_type'         => $rawRow->job_type,
                    'job_status'         => $rawRow->job_status ?? 'assigned',
                    'status_label'       => $statusLabel,
                    'source'             => $source,
                    'from'               => $from,
                    'to'                 => $to,
                    'date'               => $formattedDate,
                    'time'               => $formattedTime,
                    'passengers'         => $passengers,
                    'pass_count'         => $passCount,
                    'preview_hash'       => $previewHash,
                    // 'amount'             => round($baseFare, 2),
                    'customer_paid'      => round($totalFare, 2),
                    'driver_amount'      => round($driverEarned, 2),
                    'commission'         => round($commission, 2),
                    'tax'                => round($tax, 2),
                    'notification_count' => isset($pushData[$rawRow->id]) ? $pushData[$rawRow->id] : 0,
                    'driver'             => [
                        'id'                  => $assignedTo,
                        'name'                => $rawRow->driver_name ?? ($driverData[$assignedTo]['name'] ?? 'Unassigned'),
                        'mobile'              => $rawRow->driver_mobile ?? ($driverData[$assignedTo]['mobile'] ?? ''),
                        'cab_type'            => $driverData[$assignedTo]['cab_type'] ?? 'Standard',
                        'rating'              => $ratings[$rawRow->id] ?? null,
                        'lat'                 => $driverLoc['lat'] ?? null,
                        'lng'                 => $driverLoc['lng'] ?? null,
                        'current_state'       => $driverLoc['current_state'] ?? '',
                        'current_district'    => $driverLoc['current_district'] ?? '',
                        'current_address'     => $driverLoc['current_address'] ?? '',
                        'location_updated_at' => $driverLoc['updated_at'] ?? null,
                    ],
                    'customer'           => [
                        'id'     => $uid,
                        'name'   => $posterName,
                        'mobile' => $mobile,
                        'email'  => $custEmail,
                    ],
                ];
            }

            return response()->json([
                'status' => true,
                'data'   => array_values($finalJobs)
            ], 200);

        } catch (\Throwable $e) {
            Log::error('CRE getAssignedJobList Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch assigned jobs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getDriverLocation(Request $request)
    {
        try {
            $location = DB::table('drivers_current_location')
                ->where('user_id', $request->input('driver_id'))
                ->select('lat', 'lng')
                ->orderBy('updated_at', 'desc')
                ->first();
    
            if (!$location) {
                return response()->json(['status' => false, 'message' => 'not found']);
            }
    
            return response()->json(['status' => true, 'data' => $location]);
            
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
