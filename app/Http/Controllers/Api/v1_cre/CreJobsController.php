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

                $source = "From Website";
                if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$job->global_type) === 'customer') {
                    $source = "From Customer App";
                } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$job->global_type) === 'driver') {
                    $source = "From Driver App";
                }

                $badge = "Regular";
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

            $source = "From Website";
            if (strpos($jobNo, 'GRC') === 0 || strtolower((string)$job->global_type) === 'customer') {
                $source = "From Customer App";
            } elseif (strpos($jobNo, 'GRD') === 0 || strtolower((string)$job->global_type) === 'driver') {
                $source = "From Driver App";
            }

            $badge = "Regular";
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

            $details = [];
            if (!empty($job->user_details)) {
                $details = is_string($job->user_details) ? json_decode($job->user_details, true) : (array)$job->user_details;
            }
            if (!is_array($details)) $details = [];

            $rawPass = $job->pass_count ?? ($details['pass_count'] ?? 1);
            if (is_string($rawPass) && strtolower(trim($rawPass)) === 'mini') {
                $passengers = "Mini";
            } elseif (is_numeric($rawPass)) {
                $count = (int) $rawPass;
                $passengers = $count . ($count == 1 ? ' Passenger' : ' Passengers');
            } else {
                $passengers = (string) $rawPass;
            }

            $luggage = null;
            if ($source === "From Website") {
                $luggCount = (int) ($details['lugg_count'] ?? ($details['luggage'] ?? 0));
                $luggage   = $luggCount . ($luggCount == 1 ? ' Luggage' : ' Luggage');
            }

            $vehicleType = null;
            if (!empty($details['cab_type'])) {
                $vehicleType = $details['cab_type'];
            } elseif (!empty($details['car_type'])) {
                $vehicleType = $details['car_type'];
            } elseif (!empty($job->car_type)) {
                $vehicleType = $job->car_type;
            } elseif (!empty($job->cab_type)) {
                $vehicleType = $job->cab_type;
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
}
