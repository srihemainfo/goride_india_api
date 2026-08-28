<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\AdminApiController;

class CarPoolFollowUp extends Command
{
    protected $signature = 'carpool:followup';

    protected $description = 'Carpool follow up';

    public function handle()
    {
        $jobs = DB::table('cus_job_temp')
            ->where('global_type', 'carpool')
            ->where('job_no', 'like', 'grp%')
            ->where('created_at', '<=', now()->subHour())
            ->where('wh_notify', 0)
            ->where('isLock', 0)
            ->whereColumn('filled_seat', '<', 'pass_count')
            ->where('job_status', '!=', 'cancelled')
            ->get();

        if ($jobs->isEmpty()) {
            return Command::SUCCESS;
        }

        $adminController = app(AdminApiController::class);
        $accessToken = $adminController->getAccessToken();
        
        $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";

        $templateName = 'carpool_followup';
        $template = DB::table('wamail_templates')->where('name', $templateName)->first();

        $processedUsers = [];

        foreach ($jobs as $job) {
            if (in_array($job->user_id, $processedUsers)) {
                DB::table('cus_job_temp')->where('id', $job->id)->update(['wh_notify' => 1]);
                continue;
            }

            $processedUsers[] = $job->user_id;

            $customer = DB::table('customer_register')->where('id', $job->user_id)->first();

            if ($customer) {
                if (!empty($customer->mobile)) {
                    $cleanMobile = preg_replace('/[^0-9]/', '', $customer->mobile);
                    
                    if (!empty($cleanMobile)) {
                        $components = [];

                        if ($template && !empty($template->header_image)) {
                            $components[] = [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "image",
                                        "image" => [
                                            "link" => $template->header_image 
                                        ]
                                    ]
                                ]
                            ];
                        }

                        if ($template && !empty($template->variables_json)) {
                            $buttonsConfig = json_decode($template->variables_json, true);
                            if (!empty($buttonsConfig['buttons'])) {
                                foreach ($buttonsConfig['buttons'] as $index => $btn) {
                                    if ($btn['type'] === 'URL' && strpos($btn['url'] ?? '', '{{1}}') !== false) {
                                        $dynamicUrlVal = $job->job_no;
                                        $components[] = [
                                            "type" => "button",
                                            "sub_type" => "url",
                                            "index" => (string)$index,
                                            "parameters" => [
                                                [
                                                    "type" => "text",
                                                    "text" => (string)$dynamicUrlVal
                                                ]
                                            ]
                                        ];
                                    }
                                }
                            }
                        }

                        $templatePayload = [
                            "name" => $templateName,
                            "language" => [
                                "code" => "en_US" 
                            ]
                        ];

                        if (!empty($components)) {
                            $templatePayload["components"] = $components;
                        }

                        $payload = [
                            "messaging_product" => "whatsapp",
                            "to" => $cleanMobile,
                            "type" => "template",
                            "template" => $templatePayload
                        ];

                        $response = Http::withToken(env('FB_WHATSAPP_TOKEN'))
                            ->acceptJson()
                            ->post($url, $payload);

                        $isSuccess = $response->successful();
                        $body = $response->json();

                        DB::table('smslog')->insert([
                            'gateway' => 'fbWhatsapp',
                            'subject' => 'Carpool Followup',
                            'details' => 'Template: ' . $templateName,
                            'mobile' => $cleanMobile,
                            'ip' => '127.0.0.1',
                            'datetime' => now(),
                            'token_response' => json_encode($body),
                            'status' => $isSuccess ? 'sent' : 'failed',
                            'reference_id' => $body['messages'][0]['id'] ?? '',
                            'site' => 'CUSTOMER',
                            'REQ_Time' => now(),
                            'RES_Time' => now(),
                            'smsdetails' => json_encode($payload),
                            'smsstatus' => $isSuccess ? 'Sent' : 'Failed',
                            'smssendstatus' => $isSuccess ? '1' : '0',
                            'response' => $response->body(),
                            'isResend' => 'NO'
                        ]);
                    }
                }

                if (!empty($customer->fcm_token) && $accessToken) {
                    try {
                        $adminController->sendFCM(
                            $accessToken, 
                            $customer->fcm_token, 
                            "Seats still available!", 
                            "Share your ride with friends and contacts to fill your seats faster.", 
                            ['type' => 'carpool_followup']
                        );
                    } catch (\Throwable $e) {
                    }
                }
            }

            DB::table('cus_job_temp')->where('id', $job->id)->update(['wh_notify' => 1]);
        }

        return Command::SUCCESS;
    }
}