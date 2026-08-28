<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendJobNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobData;
    public $fcmTokens;
    public $driverIds;
    public $serviceAccount;
    public $accessToken;

    public function __construct(array $jobData, array $fcmTokens, array $serviceAccount, string $accessToken, array $driverIds = [])
    {
        $this->connection = 'sync';

        $this->jobData = $jobData;
        $this->fcmTokens = $fcmTokens;
        $this->driverIds = $driverIds;
        $this->serviceAccount = $serviceAccount;
        $this->accessToken = $accessToken;
    }

    public function handle()
    {
        $successCount = 0;
        $failureCount = 0;
        $delivered = [];
        $notDelivered = [];

        $users = DB::table('user_register')
            ->whereIn('id', $this->driverIds)
            ->select('id', 'name', 'fcm_token')
            ->get()
            ->keyBy('fcm_token');

        $title = $this->jobData['action'] == 'schedule_popup' ? '🎯 Job Waiting for You' : '🔔 New Job Arrived!';
        $body = $this->jobData['action'] == 'schedule_popup' ? 'Hurry up! Please confirm your availability within 30 seconds to take this job. 🚕' : 'A new job is available. Open the app to accept. 🏁';

        $ch_job = DB::table('cus_job_temp')->where(['id' => $this->jobData['id'], 'job_status' => 'cancelled'])->exists();

        if ($ch_job) {
            return;
        }

        foreach ($this->fcmTokens as $token) {
            $uId = $users[$token]->id ?? 0;
            $uName = $users[$token]->name ?? 'Driver';

            try {
                $response = app()->call(
                    'App\Http\Controllers\Api\CustomerAppController@sendFCM',
                    [
                        'accessToken' => $this->accessToken,
                        'fcmToken' => $token,
                        'title' => $title,
                        'body' => $body,
                        'data' => $this->jobData
                    ]
                );

                if (isset($response['name'])) {
                    $successCount++;
                    $delivered[] = ['id' => $uId, 'name' => $uName];
                } else {
                    $failureCount++;
                    $notDelivered[] = ['id' => $uId, 'name' => $uName, 'error' => 'FCM Response Error'];
                }
            } catch (\Throwable $e) {
                $failureCount++;
                $notDelivered[] = ['id' => $uId, 'name' => $uName, 'error' => $e->getMessage()];

                Log::error('Queue FCM failed', [
                    'token' => $token,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($successCount > 0 || $failureCount > 0) {
            try {
                $projectId = $this->serviceAccount['project_id'] ?? null;
                $jobNo = $this->jobData['job_no'] ?? null;

                if ($projectId && $jobNo) {
                    $collection = env('FIREBASE_COLLECTION', 'dev_jobs');
                    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:commit";

                    $fieldTransforms = [];

                    if ($successCount > 0) {
                        $fieldTransforms[] = [
                            'fieldPath' => 'success_count',
                            'increment' => ['integerValue' => (string) $successCount]
                        ];
                    }

                    if ($failureCount > 0) {
                        $fieldTransforms[] = [
                            'fieldPath' => 'failure_count',
                            'increment' => ['integerValue' => (string) $failureCount]
                        ];
                    }

                    $payload = [
                        'writes' => [
                            [
                                'transform' => [
                                    'document' => "projects/{$projectId}/databases/(default)/documents/{$collection}/{$jobNo}",
                                    'fieldTransforms' => $fieldTransforms
                                ]
                            ]
                        ]
                    ];

                    $fbResponse = Http::withToken($this->accessToken)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($url, $payload);

                    if ($fbResponse->failed()) {
                        Log::error('Firebase REST increment failed: ' . $fbResponse->body());
                    }
                }
            } catch (\Exception $updateEx) {
                Log::error('Firebase bulk increment exception: ' . $updateEx->getMessage());
            }
        }
        

        $resJson = [
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'delivered' => $delivered,
            'not_delivered' => $notDelivered,
        ];

        DB::table('push_notifications')->insert([
            'user_id' => '0',
            'sent_by' => '0',
            'title' => $title,
            'body' => $body,
            'status' => ($successCount > 0) ? 1 : 0,
            'req_json' => json_encode(['target' => 'drivers', 'job_id' => $this->jobData['id'] ?? '']),
            'res_json' => json_encode($resJson, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes' => 0,
        ]);

        if (!empty($this->driverIds)) {
            $logData = [];

            foreach ($this->driverIds as $driverId) {
                $logData[] = [
                    'user_id' => $driverId,
                    'job_id' => $this->jobData['id'],
                    'title' => $title,
                    'message' => $body,
                    'type' => $this->jobData['type'],
                    'created_at' => now()
                ];
            }

            DB::table('driver_notification_logs')->insert($logData);
        }

        Log::info('Job FCM Notification Results', [
            'job_id' => $this->jobData['id'] ?? null,
            'job_no' => $this->jobData['job_no'] ?? null,
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ]);
    }
}