<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DB;

class SignUpFollowUp extends Command
{

    protected $signature = 'signup:followup';

    protected $description = 'Sign Up Whatsapp Follow Up';
    
    public function checkWhats($ph){
        
        $controller = new Controller();
        $existsWhatsApp = $controller->checkWhatsApp(['mobile' => $ph]);
        
        return $existsWhatsApp;
    }

    public function handle()
    {
        
        // $controller = new Controller();
        
        $getdataFirst = DB::table('goride_ad_leads as gl')
            ->leftJoin('user_register as ur', function ($join) {
                    $join->whereRaw("ur.mobile LIKE CONCAT('%', gl.phone, '%')")
                         ->where('ur.deletes', '0');
                })
            ->select(
                'gl.*',
                'ur.id as user_id',
                'ur.name as user_name',
                'ur.created_at as user_created_at',
                'ur.deletes as user_delete'
            )
            ->whereBetween('gl.created_at', [
                now()->subDays(2)->startOfDay(), // 00:00:00 of 2 days ago
                now()->subDays(2)->endOfDay()    // 23:59:59 of 2 days ago
            ])
            ->inRandomOrder()
            ->orderBy('gl.id', 'DESC')
            ->limit(2)
            ->get();

        
        // dd($getdataFirst);
            
        $updateCount = 0;
        $cron_start = now();
        
        if($getdataFirst){
            
            // $email_setting = DB::table('email_config')->where('deletes', '0')->first();
            
            // dd($getdataFirst);
            
            foreach($getdataFirst as $value){
                // USER DIDN'T SIGNUP T-1
                // dd('hii');
                $ch_again = DB::table('goride_ad_leads')
                    ->where('id', $value->id)
                    ->where(function ($query) {
                        $query->where('status', '<', 1)
                              ->orWhere('status', 1);
                    })
                    ->exists();
                    
                // dd($ch_again, $value->user_id);
                
                if($value->user_id == null && $value->welcome_temp_status == 0 && !$ch_again){
                    
                    
                    if (Carbon::parse($value->created_at)->lt(Carbon::now()->subMinutes(1))) {
                        
                        goto FIRSTATTEMPT;
                    }else{
                        goto EXITATTEMPT;
                    }

                }
                
                goto EXITATTEMPT;
                
                FIRSTATTEMPT:
                // dd('first', $value);
                
                $get_temp = DB::table('whatsapp_temp')->where(['temp_type' => 'signup'])->whereNotNull('content')->first();
                
                if ($get_temp && $get_temp->isImg == 1 && $get_temp->img_con) {

                    $content = $get_temp->content;
                    $existsWhatsApp = $this->checkWhats($value->phone);
                
                    if ($existsWhatsApp) {
                
                        $url = env('SHIWhatsAppEndPoint') . 'client/sendMessage/' . env('SHIWhatsAppInstance');
                
                        $mediaResponse = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'x-api-key' => env('SHIWhatsAppAPIKey'),
                        ])->post($url, [
                            "chatId" => $value->phone . '@c.us',
                            "contentType" => "MessageMedia",
                            "content" => [
                                "mimetype" => "image/png",
                                "data" => $get_temp->img_con,
                                "filename" => "image.jpg"
                            ]
                        ]);
                
                        $textResponse = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'x-api-key' => env('SHIWhatsAppAPIKey'),
                        ])->post($url, [
                            "chatId" => $value->phone . '@c.us',
                            "contentType" => "string", 
                            "content" => $content
                        ]);
                
                        if ($textResponse->successful()) {
                
                            DB::table('goride_ad_leads')
                                ->where('id', $value->id)
                                ->update(['welcome_temp_status' => 1]);
                
                            $updateCount++;
                        }
                    }
                } else {
                    goto EXITATTEMPT;
                }

                
                // // dd($updateCount);
                EXITATTEMPT:
                
            }
            
            $cron_end = now();
            $newLead = DB::table('cron_logs')->insertGetId([
                'cron_name'         => 'goride_ad_leads_whatsapp_template',
                'table_name'         => '',
                'cron_start'         => $cron_start,
                'cron_end'        => $cron_end,
                'affected_rows'        => $updateCount,
                'error_note'   => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        if($updateCount){
            $this->info('Leads users whatsapp template send count   ---->  '. $updateCount);
        }else{
            $this->info('Leads are Up to date.');
            
        }
    }
}
