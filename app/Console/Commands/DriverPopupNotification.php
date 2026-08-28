<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseJobService;

class DriverPopupNotification extends Command
{
    protected $signature = 'driver:popup';
    
    protected $description = 'Send popup notification to drivers by topic';
    
    protected $serviceAccount;
    
    public function __construct()
    {
        parent::__construct();

        $config = storage_path('app/firebase/firebase-config-customer.json');

        $this->serviceAccount = json_decode(file_get_contents($config), true);
    }

    public function handle()
    {
        try {

            $hour = now()->format('H');
            
            \Log::info('Driver Popup Cron', [
                'time' => now()->format('Y-m-d H:i:s')
            ]);

            $topic = "driver_popup_{$hour}";
            // $topic = "driver_popup_11";
            
            // $alreadySent = DB::table('push_notifications')->whereDate('created_at', today())->where('status', 1)->where('req_json', 'like', '%"topic":"'.$topic.'"%')->exists();
            
            // if ($alreadySent) {
                
            //     $this->info("Notification already sent today for {$topic}");
                
            //     return Command::SUCCESS;
            // }

            $title = "Boost Your Earnings Today! 💰";

            $body = "More rides mean more money. Go online now and post a ride immediately!";

            $id = DB::table('push_notifications')->insertGetId([
                'title' => $title,
                'body' => $body,
                'sent_by' => 0,
                'user_id' => 0,
                'route' => null,
                'status' => 0,
                'res_json' => null,
                'req_json' => json_encode([
                    'topic' => $topic,
                    'target' => 'drivers'
                ]),
                'created_at' => now(),
                'updated_at' => now()

            ]);

            $firebase = new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );

            $response = $firebase->sendTopicNotification(
                $topic,
                $title,
                $body,
                [

                    'type' => 'sch_job_reminder',
                    'screen' => 'dashboard'

                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Update Log
            |--------------------------------------------------------------------------
            */

            DB::table('push_notifications')

                ->where('id', $id)

                ->update([

                    'status' => 1,

                    'res_json' => json_encode($response),

                    'updated_at' => now()

                ]);

            $this->info('Notification Sent'. now()->format('Y-m-d H:i:s'));

        } catch (\Exception $e) {

            DB::table('push_notifications')

                ->where('id', $id ?? 0)

                ->update([

                    'status' => 2,

                    'res_json' => json_encode([

                        'error' => $e->getMessage()

                    ]),

                    'updated_at' => now()

                ]);

            $this->error($e->getMessage());
        }
    }

    private function getAccessToken()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));

        $now = time();

        $claim = [

            'iss'   => $this->serviceAccount['client_email'],

            'scope' => 'https://www.googleapis.com/auth/cloud-platform',

            'aud'   => $this->serviceAccount['token_uri'],

            'iat'   => $now,

            'exp'   => $now + 3600

        ];

        $claimEncoded = base64_encode(json_encode($claim));

        $signatureInput = "{$header}.{$claimEncoded}";

        openssl_sign(

            $signatureInput,

            $signature,

            openssl_pkey_get_private($this->serviceAccount['private_key']),

            OPENSSL_ALGO_SHA256

        );

        $jwt = $signatureInput . "." .

            str_replace(

                ['+','/','='],

                ['-','_',''],

                base64_encode($signature)

            );

        $post = http_build_query([

            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',

            'assertion' => $jwt

        ]);

        $ch = curl_init($this->serviceAccount['token_uri']);

        curl_setopt_array($ch,[

            CURLOPT_RETURNTRANSFER=>true,

            CURLOPT_POST=>true,

            CURLOPT_POSTFIELDS=>$post

        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response,true)['access_token'] ?? null;
    }
}