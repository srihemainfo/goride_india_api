<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DB;

class CalcelFollowUp extends Command
{

    protected $signature = 'cancel-job:followup';

    protected $description = 'Command description';

    public function handle()
    {
        
        $get_data1 = DB::table('open_jobs')
            ->leftJoin('user_register', 'user_register.id', '=', 'open_jobs.user_id')
            ->select(
                'open_jobs.pickup_date',
                'open_jobs.cron_check_status',
                'open_jobs.job_no',
                'open_jobs.from_place',
                'open_jobs.to_place',
                'open_jobs.id',
                'open_jobs.user_id',
                'open_jobs.bids_details',
                'user_register.name',
                'user_register.email',
                'user_register.mobile'
            )
            // ->whereBetween('open_jobs.pickup_date', [Carbon::now()->addMinutes(10), Carbon::now()->addMinutes(15)])
            ->whereBetween('open_jobs.pickup_date', [
                Carbon::now()->addMinutes(70),
                Carbon::now()->addMinutes(75)
            ])

            ->where('open_jobs.deletes', '0')
            ->whereIn('open_jobs.cron_check_status', [0, 1])
            ->whereIn('open_jobs.job_status', ['created', 'bidding']);
        
        $get_data2 = DB::table('open_jobs')
            ->leftJoin('user_register', 'user_register.id', '=', 'open_jobs.user_id')
            ->select(
                'open_jobs.pickup_date',
                'open_jobs.cron_check_status',
                'open_jobs.job_no',
                'open_jobs.from_place',
                'open_jobs.to_place',
                'open_jobs.id',
                'open_jobs.user_id',
                'open_jobs.bids_details',
                'user_register.name',
                'user_register.email',
                'user_register.mobile'
            )
            // ->whereBetween('open_jobs.pickup_date', [Carbon::now()->subMinutes(5), Carbon::now()->addMinute()])
            ->whereBetween('open_jobs.pickup_date', [
                Carbon::now(),
                Carbon::now()->addMinutes(62)
            ])

            ->where('open_jobs.deletes', '0')
            ->whereIn('open_jobs.cron_check_status', [0, 1])
            ->whereIn('open_jobs.job_status', ['created', 'bidding']);
        
        $merged_data = $get_data1
            ->union($get_data2)
            ->orderBy('pickup_date', 'DESC')
            ->limit(10)
            ->get();
        
        // dd($merged_data);
                
        if(count($merged_data) > 0){
            foreach ($merged_data as $value) {
                $pickup = Carbon::parse($value->pickup_date);
                // dd($pickup->between(Carbon::now()->addMinutes(70), Carbon::now()->addMinutes(75)));
                if ($pickup->between(Carbon::now()->addMinutes(70), Carbon::now()->addMinutes(75)) && $value->cron_check_status == 0) {
                    // WARNATTEMPT logic here
                    // Example:
                    
                    if($value->bids_details != null){
                        
$content = '
*⏳ Job Expiring Soon! 🚖*

Job *' . $value->job_no . '* from *' . $value->from_place . '* to *' . $value->to_place . '* will expire in *10 minutes*.

If you don’t accept any bidder, it will be *automatically cancelled*. ⚠️

👉 Open the app and accept a bidder now!

– *GoRide* 🚀
';

                    }else{
                        
$content = '
*⏳ Job Expiring Soon! 🚖*

Job *' . $value->job_no . '* from *' . $value->from_place . '* to *' . $value->to_place . '* will expire in *10 minutes*.

If no bidder is accepted, it will be *automatically cancelled*. ⚠️

👉 Open the app and accept a bidder now!

– *GoRide* 🚀
';
                    }

                    $ins_con = [
                        'details' => $content,
                        'name' => $value->name,
                        'status' => 'pending',
                        'to_whatsapp' => $value->mobile,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    DB::table('whatsapp_bulk_message')->insert($ins_con);
                    
                    DB::table('open_jobs')
                        ->where('id', $value->id)
                        ->update(['cron_check_status' => 1]);

                    \Log::error('Warning message Success', [
                            'job_id' => $value->id,
                            'response_status' => '',
                            'response_body' => ''
                        ]);
                
                } 
                // else if ($pickup->lessThanOrEqualTo(Carbon::now()->addHour()) && $pickup->greaterThan(Carbon::now())) {
                elseif (
                    $pickup->greaterThanOrEqualTo(Carbon::now()) &&
                    $pickup->lessThanOrEqualTo(Carbon::now()->addMinutes(62) )
                && ($value->cron_check_status == 1 || $value->cron_check_status == 0)) {
                    
                    // dd('hii');

                    $call_cancel = Http::post(env('APP_API') . 'admin-cancel-job', [
                        'job_id'   => $value->id,
                        'user_id'  => $value->user_id,
                        'auth_key' => 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345',
                    ]);
                
                    if ($call_cancel->successful()) {
                        DB::table('open_jobs')
                            ->where('id', $value->id)
                            ->update(['cron_check_status' => 2]);
                        
                        \Log::error('Cancel API Success', [
                            'job_id' => $value->id,
                            'response_status' => $call_cancel->status(),
                            'response_body' => $call_cancel->body()
                        ]);
                    } else {
                        // Print error response for debugging
                        \Log::error('Cancel API failed', [
                            'job_id' => $value->id,
                            'response_status' => $call_cancel->status(),
                            'response_body' => $call_cancel->body()
                        ]);
                    }
                }
        
                // EXITATTEMPT logic if needed after loop
            }
        }
        
        return Command::SUCCESS;
    }
}