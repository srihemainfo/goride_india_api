<?php

namespace App\Helpers;

use Carbon\Carbon;

class WhatsAppTemplateBuilder
{
    public static function build(string $type, $user, array $data): string
    {
        return match ($type) {

            'job_cancelled_bidder' => 
"Hello {$user->name}, 👋

Your bid for job *{$data['job_no']}* has been ❌ cancelled.

📍 {$data['from']} → {$data['to']}
🕒 " . Carbon::parse($data['pickup'])->format('d M Y h:i A'),

            'job_cancelled_owner' =>
"Hello {$user->name}, 👋

Your job *{$data['job_no']}* has been ❌ cancelled successfully.

📍 {$data['from']} → {$data['to']}
🕒 " . Carbon::parse($data['pickup'])->format('d M Y h:i A'),

            default =>
"Hello {$user->name}, 👋

You have a new notification."
        };
    }
}