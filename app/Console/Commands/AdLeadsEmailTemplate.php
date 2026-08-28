<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DB;

class AdLeadsEmailTemplate extends Command
{

    protected $signature = 'leads:email-templates';

    protected $description = 'Send an Whatsapp Template for the Go Ride Leads';

    public function handle()
    {
        
        $controller = new Controller();
        
        $getdataFirst = DB::table('goride_ad_leads as gl')
            ->leftJoin('user_register as ur', function ($join) {
                $join->on(DB::raw('ur.mobile'), 'LIKE', DB::raw("CONCAT('%', gl.phone, '%')"));
            })
            ->select(
                'gl.*',
                'ur.id as user_id',
                'ur.name as user_name',
                'ur.created_at as user_created_at',
                'ur.deletes as user_delete'
            )
            // ->inRandomOrder()
            ->orderBy('gl.id', 'DESC') // Avoid ambiguous column
            ->limit(5)
            ->get()->toArray();

        
        // dd($getdataFirst);
            
        $updateCount = 0;
        $cron_start = now();
        
        if($getdataFirst){
            
            // $email_setting = DB::table('email_config')->where('deletes', '0')->first();
            
            foreach($getdataFirst as $value){
                // USER DIDN'T SIGNUP T-1
                // $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->where('status', '<', 1)->exists();
                
                $ch_again = DB::table('goride_ad_leads')
                    ->where('id', $value->id)
                    ->where(function ($query) {
                        $query->where('status', '<', 1)
                              ->orWhere('status', 1);
                    })
                    ->exists();
                
                // dd($value->user_id, !$ch_again, Carbon::parse($value->created_at)->lt(Carbon::now()->subMinutes(1)));
                if($value->user_id == null && $value->temp_status == 0 && !$ch_again){
                    
                    
                    if (Carbon::parse($value->created_at)->lt(Carbon::now()->subMinutes(1))) {
                        
                        goto FIRSTATTEMPT;
                    }else{
                        goto EXITATTEMPT;
                    }

                }
                // dd($value);
                // USER DIDN'T BUY CRM T-2
                $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->where('status', '2')->exists();
                
                // $ch_again = DB::table('goride_ad_leads')
                //     ->where('id', $value->id)
                //     ->where(function ($query) {
                //         $query->where('status', '<', 3);
                //             //   ->orWhere('status', 2);
                //     })
                //     ->exists();
                // dd(!$ch_again);
                if($value->user_id && !$ch_again){
                    // dd($value, $ch_again);
                    
                    $getCRM = DB::table('crm')->where(['userID' => $value->user_id, 'deletes' => '0'])->exists();
                    
                    if(!$getCRM){
                        
                        if (Carbon::parse($value->user_created_at)->lt(Carbon::now()->subMinutes(1))) {
                        
                            goto SECONDATTEMPT;
                        }else{
                            goto EXITATTEMPT;
                        }
                    }
                    
                    
                }
                
                // USER DIDN'T SETUP CRM T-3
                $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->where('status', '3')->exists();
                
                // $ch_again = DB::table('goride_ad_leads')
                //     ->where('id', $value->id)
                //     ->where(function ($query) {
                //         $query->where('status', '<', 3)
                //               ->orWhere('status', 3);
                //     })
                //     ->exists();
                if($value->user_id && !$ch_again){
                    
                    $getCRM_g = DB::table('crm')->where(['userID' => $value->user_id, 'crmStatus' => 'generated', 'deletes' => '0'])->exists();
                    $getCRM_p = DB::table('crm')->where(['userID' => $value->user_id, 'crmStatus' => 'pending', 'deletes' => '0'])->first();
                    
                    
                    if(!$getCRM_g && $getCRM_p){
                        
                        if (Carbon::parse($getCRM_p->createdon)->lt(Carbon::now()->subMinutes(1))) {
                        
                            goto THIRDATTEMPT;
                        }else{
                            goto EXITATTEMPT;
                        }
                    }
                }
                
                // USER DIDN'T SETUP CRM ACCOUNT T-4
                $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->whereIn('status', ['4', '5'])->exists();
                
                    
                if($value->user_id && !$ch_again){
                    
                    $getCRM_c = DB::table('crm')->where(['userID' => $value->user_id, 'crmStatus' => 'generated', 'deletes' => '0'])->count();
                    
                    // $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->whereIn('status', ['5'])->exists();
                    
                    if($getCRM_c == 1){
                        
                        $getCRM_g = DB::table('crm')->where(['userID' => $value->user_id, 'crmStatus' => 'generated', 'deletes' => '0'])->first();
                        
                        $token = env('EXPECTED_API_TOKEN');
    
                        $payload = [
                            'fullDomain' => $getCRM_g->fullDomain,
                            'type' => 'setting'
                        ];
                    
                        $response = Http::withToken($token)
                            ->post(env('API_URL') . '/getPartnerStatus', $payload);
                    
                        
                        if ($response->successful()) {
                            $data = $response->json();
                            if($data['data'] == false){
                                
                            // dd($data, 'hiii');
                                goto FOURTHATTEMPT;
                                // if (Carbon::parse($getCRM_g->createdon)->lt(Carbon::now()->subMinutes(1))) {
                                // }else{
                                //     goto EXITATTEMPT;
                                // }
                                
                            }
                        }else{
                            goto EXITATTEMPT;
                            
                        }
                    }
                    
                }
                
                
                // USER DIDN'T SETUP CRM WEBSITE T-5
                $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->whereIn('status', [ '5'])->exists();

                if($value->user_id && !$ch_again){
                    
                    $getCRM_c = DB::table('crm')->where(['userID' => $value->user_id, 'crmStatus' => 'generated', 'deletes' => '0'])->count();
                    
                    // $ch_again = DB::table('goride_ad_leads')->where(['id' => $value->id])->whereIn('status', ['5'])->exists();
                    
                    if($getCRM_c == 1){
                        
                        $getCRM_g = DB::table('crm')->where(['userID' => $value->user_id, 'crmStatus' => 'generated', 'deletes' => '0'])->first();
                        
                        $token = env('EXPECTED_API_TOKEN');
    
                        $payload = [
                            'fullDomain' => $getCRM_g->fullDomain,
                            'type' => 'website'
                        ];
                    
                        $response = Http::withToken($token)
                            ->acceptJson()
                            ->post(env('API_URL') . '/getPartnerStatus', $payload);
                    
                        if ($response->successful()) {
                            $data = $response->json();
                            
                            if($data['data'] == false){
                                
                                goto FIFTHATTEMPT;
                                // if (Carbon::parse($getCRM_g->createdon)->lt(Carbon::now()->subMinutes(1))) {
                                // }else{
                                //     goto EXITATTEMPT;
                                // }
                                
                            }else{
                                goto EXITATTEMPT;
                            }
                        }
                    }
                    
                }
                
                goto EXITATTEMPT;
                
                FIRSTATTEMPT:
                // dd('first', $value);
$content = '
👋 Hi there!

Welcome to *Go Ride Software* – the smart way to run your cab business.

✅ *30-Day Free Trial* _(limited time only)_

🧑‍💼 *Built-in CRM* to manage your drivers  
📱 *Free Driver & Passenger Apps* (Android & iOS)  
🌐 *Free Website* – your customers can even book out-of-city rides!  
✈️ *Perfect for airport rides & fleet operators*

👉 *Sign up now:* https://www.goride.run/signup

Got questions? Let’s connect for a quick 15-minute Google Meet or feel free to chat with us anytime!
';
                            
                // $subject = "You Haven\'t Registered Yet–Join GoRide Today!";
                $temp_st = '1';
                // $em_st = '1';
                
                goto WHATSAPPSEND;
                
                
                SECONDATTEMPT:
                // dd('second', $value);
$content = '
Hi ' . ucwords($value->user_name).'! 👋  
Welcome to *Go Ride Software* – the smart way to run your cab business. 🚖  

You haven’t purchased the CRM yet — kindly buy it to start managing your business.

✅ Enjoy a *30-day free trial* _(limited time offer)_  
🛒 Buy now: https://www.goride.run/pricing  

🎥 Watch tutorials:  
▶️ *CRM Setup* – Tutorial 1  
▶️ *New Booking Setup* – Tutorial 2  

💬 Need help? Chat with us or book a quick 15-min Google Meet!
';

            $temp_st = '2';
            
            goto WHATSAPPSEND;
                
                
                        
                THIRDATTEMPT:
                // dd('third', $value);
$content = '
Hi ' . ucwords($value->user_name).'! 🎉  
Your *Go Ride* account is registered! 🚖  

Your CRM setup is still pending — complete it to launch your business.

✅ Enjoy a *30-day free trial* _(limited time)_  
🔧 Setup here: https://www.goride.run/pricing  

🎥 Watch tutorials:  
▶️ *CRM Setup* – Tutorial 1  
▶️ *New Booking Setup* – Tutorial 2  

💬 Need help? Chat with us or book a quick 15-min Google Meet!
';

            $temp_st = '3';
            
            goto WHATSAPPSEND;
            
            
            FOURTHATTEMPT:
            // dd('FOURTH', $value);
$content = '
Hi ' . ucwords($value->user_name).'! 🎉  
Your *Go Ride* account is registered! 🚖  

Your Insite CRM setup is still pending — complete it to launch your business.

✅ Enjoy a *30-day free trial* _(limited time)_  
🔧 Setup here: https://www.goride.run/pricing  

🎥 Watch tutorials:  
▶️ *CRM Setup* – Tutorial 1  
▶️ *New Booking Setup* – Tutorial 2  

💬 Need help? Chat with us or book a quick 15-min Google Meet!
';

        $temp_st = '4';
        
            goto WHATSAPPSEND;
            
            FIFTHATTEMPT:
            // dd('FIFTH', $value);
$content = '
Hi ' . ucwords($value->user_name).'! 🎉  
Your *Go Ride* account is registered! 🚖  

Your CRM website setup is still pending — complete it to launch your business.

✅ Enjoy a *30-day free trial* _(limited time)_  
🔧 Setup here: https://www.goride.run/pricing  

🎥 Watch tutorials:  
▶️ *CRM Setup* – Tutorial 1  
▶️ *New Booking Setup* – Tutorial 2  

💬 Need help? Chat with us or book a quick 15-min Google Meet!
';

        $temp_st = '5';
        
            goto WHATSAPPSEND;
                
                WHATSAPPSEND:
                // // dd($value->phone);
                if(true){
                    
                    $url = env('SHIWhatsAppEndPoint').'client/sendMessage/'.env('SHIWhatsAppInstance');
                    
                    $response = http::withHeaders([
                        'Content-Type' => 'application/json',
                        'x-api-key' => env('SHIWhatsAppAPIKey'),
                    ])->post($url, [
                        "chatId" => $value->phone.'@c.us',
                        // // "chatId" => "918825903112@c.us",
                        // "chatId" => "919585769163@c.us",
                        "contentType" => "string",
                        "content" => $content
                    ]);
                    
                    if($response->successful()){
                        
                        $updateCount ++;
                        
                        $updateLead = DB::table('goride_ad_leads')->where(['id' => $value->id])->update(['temp_status' => $temp_st, 'status' => $temp_st]);
                    }

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
