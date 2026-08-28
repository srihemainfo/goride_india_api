<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DeductAmountDriver extends Command
{
    protected $signature = 'DeductAmount:Driver';
    protected $description = 'Deduct driver wallet amount for assigned jobs';
    
    public $serviceAccountPath;
    public $serviceAccount;
    public $serviceAccountPath2;
    public $serviceAccount2;
    
    public function __construct()
    {
        parent::__construct();
    
        // Correct path to storage/app/firebase/firebase-config.json
        $this->serviceAccountPath = storage_path('app/firebase/firebase-config-customer.json');
    
        $this->serviceAccountPath2 = storage_path('app/firebase/firebase-config-customer-schedule.json');
    
        if (!file_exists($this->serviceAccountPath)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit;
        }
    
        if (!file_exists($this->serviceAccountPath2)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit;
        }
    
        $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
    
        $this->serviceAccount2 = json_decode(file_get_contents($this->serviceAccountPath2), true);
    }
    
    public function getAccessToken()
    {
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
    
        // Request access token
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
    
    public function sendFCM($accessToken, $fcmToken, $title, $body, $data = [])
    {
        // Ensure all data values are strings
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
                'notification' => [ // 👈 required for mobile push
                    'title' => $title,
                    'body'  => $body,
                    // 'image' => 'https://airportrides-storage.s3.amazonaws.com/cus_app/images/walletdebitnotification_6a0188e4afb6f.jpg' 
                ],
                'android' => [
                    'priority' => 'high',
                    'ttl' => '86400s',
                    'notification' => [
                        'channel_id' => 'new_job_channel',
                        'sound' => 'custom_notification',
                        'color' => '#FF6B35',
                        // 'image' => 'https://airportrides-storage.s3.amazonaws.com/cus_app/images/walletdebitnotification_6a0188e4afb6f.jpg' 
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
        
        $payload['message']['notification']['image'] = 'https://airportrides-storage.s3.amazonaws.com/cus_app/images/walletdebitnotification_6a0188e4afb6f.jpg';
        
        $payload['message']['android']['notification']['image'] = 'https://airportrides-storage.s3.amazonaws.com/cus_app/images/walletdebitnotification_6a0188e4afb6f.jpg';
        
        $payload['message']['apns']['payload']['aps']['mutable-content'] = 1;
        $payload['message']['apns']['fcm_options']['image'] = 'https://airportrides-storage.s3.amazonaws.com/cus_app/images/walletdebitnotification_6a0188e4afb6f.jpg';
    
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

    public function handle()
    {
        $this->info("🚀 CRON STARTED");
    
        $now = \Carbon\Carbon::now('Asia/Kolkata');
    
        $start = $now;
        $end   = $now->copy()->addMinutes(60);
    
        $this->info("Time Window: {$start} → {$end}");
        
    
        DB::table('cus_job_temp')
            ->where('job_status', 'accept')
            ->where('isDeduct', 0)
            ->whereNot('deductAmt', 0)
            ->whereBetween('pickup_date', [$start, $end])
            ->orderBy('id')
            ->chunkById(100, function ($jobs) {
    
                foreach ($jobs as $job) {
    
                    $this->info("👉 Processing Job ID: {$job->id}");
    
                    DB::beginTransaction();
    
                    try {
    
                        $jobRow = DB::table('cus_job_temp')
                            ->where('id', $job->id)
                            ->lockForUpdate()
                            ->first();
    
                        if (!$jobRow || $jobRow->isDeduct == 1) {
                            $this->warn("⛔ Skipped (Already Deducted)");
                            DB::rollBack();
                            continue;
                        }
                        
                        $exists = DB::table('walletBalance_history')
                            ->where('userid', $jobRow->assigned_to)
                            ->where('reference_id', $jobRow->id)
                            ->where('reference_table', 'cus_job_temp')
                            ->where('transaction_type', 'DEBIT')
                            ->exists();
    
                        if ($exists) {
    
                            DB::table('cus_job_temp')
                                ->where('id', $jobRow->id)
                                ->update(['isDeduct' => 1]);
    
                            DB::commit();
                            continue;
                        }
    
                        $user = DB::table('user_register')
                            ->where('id', $jobRow->assigned_to)
                            ->where('deletes', '0')
                            ->lockForUpdate()
                            ->first();
    
                        if (!$user) {
                            $this->error("❌ User Not Found");
                            DB::rollBack();
                            continue;
                        }
    
                        $walletBalance  = (float) $user->walletBalance;
                        $deductAmt      = (float) $jobRow->deductAmt;
                        $closingBalance = $walletBalance - $deductAmt;
    
                        DB::table('user_register')
                            ->where('id', $user->id)
                            ->update([
                                'walletBalance' => $closingBalance
                            ]);
    
                        DB::table('walletBalance_history')->insert([
                            "userid" => $user->id,
                            "uname" => $user->name ?? '',
                            "umobile" => $user->mobile ?? '',
                            "uemail" => $user->email ?? '',
                            'opening_balance' => $walletBalance,
                            'total' => $deductAmt,
                            'closeing_balance' => $closingBalance,
                            'point_type' => 'WALLET',
                            'transaction_type' => 'DEBIT',
                            'reward_type' => 'JOB',
                            'reference_id' => $jobRow->id,
                            'reference_table' => 'cus_job_temp',
                            'ip' => 'CRON',
                            'createdon' => now()
                        ]);
    
                        DB::table('cus_job_temp')
                            ->where('id', $jobRow->id)
                            ->update(['isDeduct' => 1]);
    
                        DB::commit();
                        
                        if (!empty($user->fcm_token)) {

                            $accessToken = $this->getAccessToken();
                        
                            $title = "Wallet Amount Deducted";
                        
                            $body = "₹{$deductAmt} has been deducted from your wallet for the assigned trip. Your current wallet balance is ₹{$closingBalance}.";
                        
                            $this->sendFCM(
                                $accessToken,
                                $user->fcm_token,
                                $title,
                                $body,
                                [
                                    'type' => 'wallet_deduction',
                                    'job_id' => $jobRow->id,
                                    'deduct_amount' => $deductAmt,
                                    'wallet_balance' => $closingBalance,
                                ]
                            );
                        }
    
                        $this->info("✅ Deducted ₹{$deductAmt} from User {$user->id}");
    
                    } catch (\Exception $e) {
                        DB::rollBack();
    
                        $this->error("🔥 Error: " . $e->getMessage());
                    }
                }
            });
    
        $this->info("✅ CRON COMPLETED");
    
        return Command::SUCCESS;
    }
    
    
}