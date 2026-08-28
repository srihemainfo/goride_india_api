<?php

namespace App\Jobs;

use Exception;
use Throwable;
use App\Models\PushAutomationRule;
use App\Models\PushNotificationLog;
use App\Services\PushNotificationService;
use Illuminate\Support\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAutomationPush implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $tries = 5;

    public $backoff = [
        60,
        300,
        600,
        1800,
        3600
    ];

    public $timeout = 120;
    public $ruleId;
    public $userId;
    public $extra;

    public function __construct($ruleId, $userId, $extra = [])
    {
        $this->ruleId = $ruleId;
        $this->userId = $userId;
        $this->extra = $extra;
    }

    public function handle(): void
    {
        Log::info('Automation push job started', [

            'rule_id' => $this->ruleId,

            'user_id' => $this->userId,

            'attempt' => $this->attempts()
        ]);

        try {

            $user = DB::table('customer_register')
                ->where('id', $this->userId)
                ->first();

            if (!$user) {

                Log::warning('Automation user not found', [

                    'user_id' => $this->userId
                ]);

                return;
            }

            Log::info('Automation user loaded', [

                'user_id' => $user->id
            ]);

            $rule = PushAutomationRule::find(
                $this->ruleId
            );

            if (!$rule) {

                Log::warning('Automation rule not found', [

                    'rule_id' => $this->ruleId
                ]);

                return;
            }

            Log::info('Automation rule loaded', [

                'rule_id' => $rule->id,

                'event' => $rule->event
            ]);

            $alreadySent = PushNotificationLog::where([

                'user_id' => $user->id,

                'rule_id' => $rule->id

            ])->exists();

            if ($alreadySent && $rule->event_type == 'single') {

                Log::warning('Push already sent', [

                    'user_id' => $user->id,

                    'rule_id' => $rule->id
                ]);

                return;
            }
            
                
            $conditionPassed = $this->checkConditions(
                $user,
                $rule
            );


            Log::info('Condition check result', [

                'passed' => $conditionPassed
            ]);

            if (!$conditionPassed) {

                Log::warning('Automation conditions failed', [

                    'user_id' => $user->id,

                    'rule_id' => $rule->id
                ]);

                return;
            }

            Log::info('Sending push notification');

            $pushService = new PushNotificationService();

            $sent = $pushService->send(

                $user->id,

                $rule->title,

                $rule->message,

                [
                    // 'screen' => $rule->redirect,
                    'screen' => 'home',
                    'event' => $rule->event
                ]
            );

            if (!$sent) {

                Log::error('Push sending failed');

                throw new Exception(
                    'Firebase push sending failed'
                );
            }

            Log::info('Push sent successfully');

            PushNotificationLog::create([

                'user_id' => $user->id,

                'rule_id' => $rule->id,

                'event' => $rule->event,

                'status' => 'sent',

                'sent_at' => now()
            ]);

            Log::info('Push log saved successfully');

        } catch (Exception $e) {

            Log::error('Automation push failed', [

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile(),

                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    private function checkConditions($user, $rule)
    {
        try {
            
            $conditions = $rule->conditions;
            
            Log::info('Checking automation conditions', [
                'conditions' => $conditions,
                'incoming_event_name' => $rule->event 
            ]);
    
            if (!$conditions) {
                Log::info('No conditions found');
                return true;
            }

            if ($rule->event == 'otp_verified') {
                if ($user->name != null && $user->name != '') {
                    Log::warning('User already completed profile', ['user_id' => $user->id]);
                    return false;
                }
            }

            if ($rule->event == 'signup_completed') {
                $rideCount = DB::table('cus_job_temp')
                    ->where('user_id', $user->id)
                    ->count();
    
                Log::info('Ride count checked', [
                    'ride_count' => $rideCount
                ]);
    
                if ($rideCount > 0) {
                    Log::warning('User already booked ride', ['user_id' => $user->id]);
                    return false;
                }
            }

            if ($rule->event == 'ride_price_checked') {
                $getLastCheck = DB::table('user_activity_log')
                    ->where('user_id', $user->id)
                    ->latest('id') 
                    ->first();
            
                if ($getLastCheck && isset($getLastCheck->meta)) {
                    $meta = is_string($getLastCheck->meta) ? json_decode($getLastCheck->meta, false) : $getLastCheck->meta;
                    
                    if (isset($meta->from_place_id, $meta->to_place_id)) {
                        $rideBooked = DB::table('cus_job_temp')
                            ->where([
                                'from_place_id' => $meta->from_place_id,
                                'to_place_id'   => $meta->to_place_id,
                                'user_id'       => $user->id
                            ])
                            ->where('pickup_date', '>=', Carbon::now()->toDateTimeString())
                            ->where('created_at', '>=', Carbon::now()->subMinutes($rule->delay_minutes))
                            ->whereDate('created_at', Carbon::today()) 
                            ->exists();
                    
                        if ($rideBooked) {
                            Log::warning('Ride already booked within the allowed delay window');
                            return false;
                        }
                    }
                }
            }
            
            if ($rule->event == 'ride_posted') {
                $latestRide = DB::table('cus_job_temp')
                    ->where('user_id', $user->id)
                    ->where('pickup_date', '>=', Carbon::now())
                    ->latest('created_at')
                    ->first();
                    
                Log::info('Checking latest ride record:', ['latest_ride' => $latestRide]);
            
                if ($latestRide) {
                    $isNotCreated = ($latestRide->job_status != 'created');
                    $isWithinDelayWindow = (Carbon::parse($latestRide->created_at) >= Carbon::now()->subMinutes($rule->delay_minutes));
    
                    if ($isNotCreated && $isWithinDelayWindow) {
                        return false;
                    }
                }
            }

            if ($rule->event == 'route_selected') {
                $rideBooked = DB::table('cus_job_temp')
                    ->where('user_id', $user->id)
                    ->exists();
            
                if ($rideBooked) {
                    return false;
                }
            }
            
            if ($rule->event == 'before_one_pick_indicate') {

                $ride = DB::table('cus_job_temp')
                    ->where('id', $this->extra['ride_id'])
                    ->first();
            
                if (!$ride) {
            
                    return false;
                }
            
                if ($ride->status == 'completed') {
            
                    return false;
                }
            
                $pickupTime = Carbon\Carbon::parse(
                    $ride->pickup_date
                );
            
                $minutes = now()->diffInMinutes(
                    $pickupTime,
                    false
                );
            
                Log::info('Pickup reminder check', [
            
                    'minutes_left' => $minutes
                ]);
            
                if ($minutes > 60 || $minutes < 0) {
            
                    return false;
                }
            }
            
            if ($rule->event == 'carpool_pick_hour_before_host') {

                $ride = DB::table('cus_job_temp')
                    ->where('id', $this->extra['ride_id'])
                    ->first();
            
                if (!$ride) {
            
                    return false;
                }
            
                if ($ride->status == 'completed') {
            
                    return false;
                }
            
                $pickupTime = Carbon\Carbon::parse(
                    $ride->pickup_date
                );
            
                $minutes = now()->diffInMinutes(
                    $pickupTime,
                    false
                );
            
                Log::info('Pickup reminder check', [
            
                    'minutes_left' => $minutes
                ]);
            
                if ($minutes > 60 || $minutes < 0) {
            
                    return false;
                }
            }
            
            if ($rule->event == 'carpool_pick_hour_before_pass') {

                $ride = DB::table('cus_job_temp')
                    ->where('id', $this->extra['ride_id'])
                    ->first();
            
                if (!$ride) {
            
                    return false;
                }
            
                if ($ride->status == 'completed') {
            
                    return false;
                }
            
                $pickupTime = Carbon\Carbon::parse(
                    $ride->pickup_date
                );
            
                $minutes = now()->diffInMinutes(
                    $pickupTime,
                    false
                );
            
                Log::info('Pickup reminder check', [
            
                    'minutes_left' => $minutes
                ]);
            
                if ($minutes > 60 || $minutes < 0) {
            
                    return false;
                }
            }
            
            Log::info('All conditions passed');
            return true;
    
        } catch (\Exception $e) {
            
            Log::error('Error inside checkConditions: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => isset($user->id) ? $user->id : null,
                'event' => isset($rule->event) ? $rule->event : null
            ]);
    
            // Fail safely. Return false so the automation doesn't fire broken workflows.
            return false;
        }
    }

    public function failed(Throwable $exception)
    {
        Log::critical('Automation job permanently failed', [

            'rule_id' => $this->ruleId,

            'user_id' => $this->userId,

            'message' => $exception->getMessage()
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save Failed Log
        |--------------------------------------------------------------------------
        */

        PushNotificationLog::create([

            'user_id' => $this->userId,

            'rule_id' => $this->ruleId,

            'event' => 'failed',

            'status' => 'failed',

            'sent_at' => now()
        ]);
    }
}