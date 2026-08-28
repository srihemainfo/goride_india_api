<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Helpers\WhatsAppTemplateBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $type,
        public array $recipients,
        public array $payload
    ) {}

    public function handle()
    {
        $users = DB::table('user_register')
            ->whereIn('id', $this->recipients)
            ->where('deletes', '0')
            ->get();

        foreach ($users as $user) {

            if (!Controller::checkWhatsApp(['mobile' => $user->mobile])) {
                continue;
            }

            $message = WhatsAppTemplateBuilder::build(
                $this->type,
                $user,
                $this->payload
            );

            try {
                Controller::sendNotification([
                    'mobile'        => $user->mobile,
                    'templateName'  => 'national_draw_verification',
                    'language'      => 'en',
                    'messages'      => $message,
                    'resend'        => false
                ]);
            } catch (\Throwable $e) {
                Log::error('WhatsApp Queue Error', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage()
                ]);
            }
        }
    }
}