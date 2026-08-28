<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\FirebaseJobService;

class SocietyGroupService
{
    
    public $serviceAccountPath;
    public $serviceAccount;
    public $serviceAccountPath2;
    public $serviceAccount2;
    public $razorpay;
    
    public function __construct()
    {
        
        $this->razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
        
        $this->serviceAccountPath = storage_path('app/firebase/firebase-config-customer.json');
        
        $this->serviceAccountPath2 = storage_path('app/firebase/firebase-config-customer-schedule.json');
    
        if (!file_exists($this->serviceAccountPath)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
        }
        
        if (!file_exists($this->serviceAccountPath2)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
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
    
    public function getAccessToken2()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));
    
        $now = time();
        $claimSet = [
            'iss'   => $this->serviceAccount2['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => $this->serviceAccount2['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600
        ];
    
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput  = "$header.$claimSetEncoded";
    
        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount2['private_key']),
            OPENSSL_ALGO_SHA256
        );
    
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Request access token
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);
    
        $ch = curl_init($this->serviceAccount2['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }

    public function shareJobToMyGroups($jobId, $userId)
    {

        try {

            $groups = DB::table('group_members AS gm')
                ->join('society_groups AS sg', 'sg.id', '=', 'gm.group_id')
                ->where('gm.user_id', $userId)
                ->where('gm.status', 'approved')
                ->where('sg.status', 'approved')
                ->whereNull('sg.deleted_at')
                ->pluck('gm.group_id')
                ->toArray();

            if (empty($groups)) {
                return;
            }
            
            $insertData = [];

            foreach ($groups as $groupId) {

                $insertData[] = [
                    'job_id' => $jobId,
                    'group_id' => $groupId,
                    'shared_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

            }

            DB::table('group_job_shares')
                ->insertOrIgnore($insertData);
            
            
            $firebase = new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
            
            $title = "🚖 New Ride Request";
            
            $body = "One of your society members has posted a new ride request.";
            
            foreach ($groups as $groupId) {
            
                try {
            
                    $topic = "group_job_notify_" . env('APP_ENV') . "_{$groupId}";
            
                    $notificationId = DB::table('push_notifications')->insertGetId([
            
                        'title' => $title,
            
                        'body' => $body,
            
                        'sent_by' => 0,
            
                        'user_id' => 0,
            
                        'route' => 'society/job',
            
                        'status' => 0,
            
                        'req_json' => json_encode([
            
                            'topic' => $topic,
            
                            'target' => 'customer',
            
                            'job_id' => $jobId,
            
                            'group_id' => $groupId
            
                        ]),
            
                        'created_at' => now(),
            
                        'updated_at' => now()
            
                    ]);
            
                    $response = $firebase->sendTopicNotification(
                        $topic,
                        $title,
                        $body,
                        [
                            'type' => 'society_job',
                            'screen' => 'society_jobs',
                            'job_id' => (string)$jobId,
                            'group_id' => (string)$groupId
                        ]
            
                    );
            
                    DB::table('push_notifications')
                        ->where('id', $notificationId)
                        ->update([
                            'status' => 1,
                            'res_json' => json_encode($response),
                            'updated_at' => now()
                        ]);
            
                } catch (\Exception $e) {
            
                    Log::error('Society Job Notification Error', [
                        'group_id' => $groupId,
                        'job_id' => $jobId,
                        'message' => $e->getMessage()
                    ]);
            
                    if (!empty($notificationId)) {
                        DB::table('push_notifications')
                            ->where('id', $notificationId)
                            ->update([
                                'status' => 2,
                                'res_json' => json_encode([
                                    'error' => $e->getMessage()
                                ]),
                                'updated_at' => now()
                            ]);
            
                    }
            
                }
            
            }

            DB::table('group_job_shares')
                ->where('job_id', $jobId)
                ->whereIn('group_id', $groups)
                ->update([
                    'notification_sent' => 1,
                    'notification_sent_at' => now(),
                    'updated_at' => now()
                ]);

        } catch (\Exception $e) {

            Log::error('Society Share Error', [
                'job_id' => $jobId,
                'user_id' => $userId,
                'message' => $e->getMessage()
            ]);

        }

    }

}