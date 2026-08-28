<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Api\CustomerAppController;
use Illuminate\Support\Facades\Log;

class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $type,
        public array $userIds,
        public string $title,
        public string $body
    ) {}

    public function handle()
    {
        $controller = app(CustomerAppController::class);
        
        if($this->type != 'job_cancelled_driver'){
            
            $tokens = $controller->getFcm($this->userIds);
        }else{
            $tokens = $this->userIds;
            $this->type = 'job_cancelled';
        }
        

        if (empty($tokens)) {
            return;
        }

        $accessToken = $controller->getAccessToken();

        foreach ($tokens as $token) {
            try {
                $controller->sendFCM(
                    $accessToken,
                    $token,
                    $this->title,
                    $this->body,
                    ['type' => $this->type]
                );
            } catch (\Throwable $e) {
                Log::error('FCM Queue Error', [
                    'token' => $token,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}