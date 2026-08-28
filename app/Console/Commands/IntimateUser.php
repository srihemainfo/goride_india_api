<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IntimateUser extends Command
{
    protected $signature = 'send:reminders';

    protected $description = 'Send push notifications directly using FCM HTTP v1 API';

    protected $serviceAccountPath;
    protected $serviceAccount;

    public function __construct()
    {
        parent::__construct();

        $this->serviceAccountPath = storage_path('app/firebase/firebase-config-customer.json');
        
        if (file_exists($this->serviceAccountPath)) {
            $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        }
    }

    public function handle()
    {
        Log::info('IntimateUser Command Started');

        if (!$this->serviceAccount) {
            $this->error('Firebase service account credentials not found.');
            Log::error('Firebase service account credentials not found at: ' . $this->serviceAccountPath);
            return Command::FAILURE;
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $currentDate = $now->format('m-d');
        $currentDay = $now->format('l');

        Log::info("Current Time: {$currentTime}, Date: {$currentDate}, Day: {$currentDay}");

        $rules = DB::table('push_automation_rules')
            ->where('is_active', 1)
            ->whereIn('event', ['birthday_greeting', 'insurance_reminder', 'dl_reminder', 'spin_intimation'])
            ->get();
            
        Log::info("Found active rules: " . $rules->count());

        foreach ($rules as $rule) {
            Log::info("Processing Rule ID: {$rule->id} - {$rule->event}");

            $conditions = json_decode($rule->conditions, true);
            $targetTime = $conditions['time'] ?? null;
            $days = $conditions['days'] ?? [];
            $type = $conditions['type'] ?? '';

            if ($targetTime && $targetTime !== $currentTime) {
                Log::info("Skipping Rule ID: {$rule->id} - Time mismatch. Target: {$targetTime}");
                continue;
            }

            if (!empty($days) && !in_array($currentDay, $days)) {
                Log::info("Skipping Rule ID: {$rule->id} - Day mismatch. Target Days: " . implode(',', $days));
                continue;
            }

            $usersQuery = DB::table('user_register')
                ->select('user_register.id', 'user_register.fcm_token')
                ->whereNotNull('user_register.fcm_token');

            if ($rule->event === 'birthday_greeting') {
                $usersQuery->whereRaw("DATE_FORMAT(user_register.dob, '%m-%d') = ?", [$currentDate]);
                Log::info("Rule ID: {$rule->id} - Applied birthday filter for {$currentDate}");
            } elseif ($rule->event === 'insurance_reminder') {
                $daysToAdd = 5;
                if (preg_match('/Before (\d+) Days/i', $type, $matches)) {
                    $daysToAdd = (int)$matches[1];
                }
                $targetDate = Carbon::now()->addDays($daysToAdd)->format('Y-m-d');
                $usersQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(vehicle_details, '$.insurance_details.insurance_exp_date')) = ?", [$targetDate]);
                Log::info("Rule ID: {$rule->id} - Applied insurance expiry filter for {$targetDate}");
            } elseif ($rule->event === 'dl_reminder') {
                $daysToAdd = 5;
                if (preg_match('/Before (\d+) Days/i', $type, $matches)) {
                    $daysToAdd = (int)$matches[1];
                }
                $targetDate = Carbon::now()->addDays($daysToAdd)->format('Y-m-d');
                $usersQuery->join('ocr_request', 'user_register.id', '=', 'ocr_request.user_id')
                           ->where('ocr_request.doc_type', 'DRIVING_LICENSE')
                           ->whereNull('ocr_request.global_type')
                           ->whereDate('ocr_request.doc_expiry', $targetDate);
                Log::info("Rule ID: {$rule->id} - Applied DL expiry filter for {$targetDate}");
            } elseif ($rule->event === 'spin_intimation') {
                $todayDate = Carbon::now()->format('Y-m-d');
                $usersQuery->whereNotIn('user_register.id', function($query) use ($todayDate) {
                    $query->select('user_id')
                          ->from('daily_spin_tracker')
                          ->where('spin_date', $todayDate)
                          ->where(function($q) {
                              $q->whereNull('slot_data')
                                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slot_data, '$.type')) != 'free_spin'");
                          });
                });
                Log::info("Rule ID: {$rule->id} - Applied spin intimation filter for {$todayDate}");
            } else {
                continue;
            }

            $users = $usersQuery->get();
            Log::info("Rule ID: {$rule->id} - Target users found: " . $users->count());

            if ($users->isEmpty()) {
                continue;
            }

            Log::info("Generating FCM Access Token for Rule ID: {$rule->id}");
            $accessToken = $this->getAccessToken();

            $sentCount = 0;
            $skippedCount = 0;
            $failedCount = 0;

            foreach ($users as $user) {
                if ($rule->event_type === 'single') {
                    $alreadySent = DB::table('push_notification_logs')
                        ->where('user_id', $user->id)
                        ->where('rule_id', $rule->id)
                        ->where('global_type', 'driver')
                        ->exists();

                    if ($alreadySent) {
                        $skippedCount++;
                        Log::debug("User ID: {$user->id} - Skipped (Single event already sent)");
                        continue; 
                    }
                }

                $dataPayload = [
                    'redirect' => $rule->redirect ?? '',
                    'event' => $rule->event ?? ''
                ];

                Log::debug("Sending FCM to User ID: {$user->id}");

                $response = $this->sendFCM(
                    $accessToken,
                    $user->fcm_token,
                    $rule->title,
                    $rule->message,
                    $dataPayload
                );

                $status = isset($response['error']) ? 'failed' : 'sent';

                if ($status === 'sent') {
                    $sentCount++;
                } else {
                    $failedCount++;
                    Log::error("FCM Send Failed for User ID: {$user->id}", ['error' => $response['error']]);
                }

                DB::table('push_notification_logs')->insert([
                    'global_type' => 'driver',
                    'user_id' => $user->id,
                    'rule_id' => $rule->id,
                    'event' => $rule->event,
                    'status' => $status,
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::info("Rule ID: {$rule->id} Execution Summary - Sent: {$sentCount}, Skipped: {$skippedCount}, Failed: {$failedCount}");
        }

        $this->info('Push reminders processed successfully.');
        Log::info('IntimateUser Command Completed successfully');
        
        return Command::SUCCESS;
    }

    private function getAccessToken()
    {
        if (Cache::has('firebase_access_token')) {
            Log::debug('Using cached Firebase access token');
            return Cache::get('firebase_access_token');
        }

        Log::debug('Generating new Firebase access token via JWT');

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
        $signatureInput = "$header.$claimSetEncoded";

        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount['private_key']),
            OPENSSL_ALGO_SHA256
        );

        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        $ch = curl_init($this->serviceAccount['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        $token = $responseData['access_token'] ?? null;

        if ($token) {
            Cache::put('firebase_access_token', $token, now()->addMinutes(55));
            Log::debug('New Firebase access token cached');
        } else {
            Log::error('Failed to generate Firebase access token', ['response' => $responseData]);
        }

        return $token;
    }

    private function sendFCM($accessToken, $fcmToken, $title, $body, $data = [])
    {
        $stringData = [];
        foreach ($data as $key => $value) {
            $validKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
            $stringData[$validKey] = (string) $value;
        }

        $stringData['title'] = $title;
        $stringData['body'] = $body;

        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->serviceAccount['project_id'] . '/messages:send';

        $payload = [
            'validate_only' => false,
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'ttl' => '86400s',
                    'notification' => [
                        'channel_id' => 'new_job_channel',
                        'sound' => 'custom_notification',
                        'color' => '#FF6B35',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'sound' => 'custom_notification.wav',
                            'badge' => 1
                        ]
                    ]
                ],
                'data' => $stringData
            ]
        ];

        if (isset($data['image']) && !empty($data['image'])) {
            $payload['message']['notification']['image'] = $data['image'];
            $payload['message']['android']['notification']['image'] = $data['image'];
            $payload['message']['apns']['payload']['aps']['mutable-content'] = 1;
            $payload['message']['apns']['fcm_options']['image'] = $data['image'];
        }

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
}