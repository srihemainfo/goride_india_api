<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PushAutomationRule;
use App\Jobs\ProcessAutomationPush;

class AutomationEventService
{
    public static function trigger($event, $userId, $extra = [])
    {
        $rules = PushAutomationRule::where('event', $event)
            ->where('is_active', 1)
            ->get();

        if ($rules->isEmpty()) {
            \Log::warning('No automation rules found', [
                'event' => $event
            ]);
            return;
        }

        foreach ($rules as $rule) {
            \Log::info('Processing automation rule', [
                'event' => $rule->event,
                'user_id' => $userId
            ]);

            if (in_array($rule->event, [
                'before_one_pick_indicate',
                'carpool_pick_hour_before_host',
                'carpool_pick_hour_before_pass'
            ])) {
                $ride = DB::table('cus_job_temp')
                    ->where('id', $extra['ride_id'] ?? 0)
                    ->first();

                if (!$ride) {
                    \Log::warning('Ride not found for automation');
                    continue;
                }

                $pickupTime = Carbon::parse($ride->pickup_date);
                $conditions = $rule->conditions;
                $beforeMinutes = $conditions['check'] ?? 60;
                $triggerTime = $pickupTime->copy()->subMinutes($beforeMinutes);
                $graceMinutes = 30;

                if (now()->gt($triggerTime->copy()->addMinutes($graceMinutes))) {
                    \Log::warning('Pickup reminder skipped. Grace time exceeded.', [
                        'trigger_time' => $triggerTime,
                        'current_time' => now()
                    ]);
                    continue;
                }

                ProcessAutomationPush::dispatch($rule->id, $userId, $extra)
                    ->delay($triggerTime);

                \Log::info('Dynamic pickup reminder queued', [
                    'pickup_time' => $ride->pickup_date,
                    'trigger_time' => $triggerTime
                ]);

                continue;
            }

            ProcessAutomationPush::dispatch($rule->id, $userId, $extra)
                ->delay(now()->addMinutes($rule->delay_minutes));

            \Log::info('Automation event queued', [
                'event' => $event,
                'user_id' => $userId,
                'delay_minutes' => $rule->delay_minutes
            ]);
        }
    }

    public static function processCampaigns()
    {
        $rules = PushAutomationRule::where('is_active', 1)
            ->whereIn('event', [
                'daily_bonus_reminder',
                'weekly_referral_reminder',
                'weekly_pool_awareness',
                'host_profile_reminder'
            ])
            ->get();

        if ($rules->isEmpty()) {
            \Log::warning('No campaign rules found');
            return;
        }

        foreach ($rules as $rule) {
            $conditions = $rule->conditions;

            if (!$conditions) {
                continue;
            }

            $sendTime = $conditions['time'] ?? null;

            if (!$sendTime) {
                continue;
            }

            $sendDateTime = now()->format('Y-m-d') . ' ' . $sendTime . ':00';

            $delaySeconds = now()->diffInSeconds(Carbon::parse($sendDateTime), false);

            if ($delaySeconds < 0) {
                \Log::warning('Campaign skipped. Time already passed.', [
                    'event' => $rule->event,
                    'time' => $sendTime
                ]);
                continue;
            }

            if ($conditions['type'] == 'daily') {
                self::processRule($rule, $delaySeconds);
                continue;
            }

            if ($conditions['type'] == 'weekly') {
                $days = $conditions['days'] ?? [];
                $currentDay = now()->format('l');

                if (in_array($currentDay, $days)) {
                    self::processRule($rule, $delaySeconds);
                }
            }
        }
    }

    private static function processRule($rule, $delaySeconds)
    {
        \Log::info('Processing campaign rule', [
            'event' => $rule->event,
            'delay_seconds' => $delaySeconds
        ]);

        if ($rule->event == 'daily_bonus_reminder') {
            DB::table('customer_register')
                ->where('cash_points', 1000)
                ->where('status', '0')
                ->where('deletes', '0')
                ->orderBy('id')
                ->chunk(500, function ($users) use ($rule, $delaySeconds) {
                    foreach ($users as $user) {
                        ProcessAutomationPush::dispatch($rule->id, $user->id)
                            ->delay(now()->addSeconds($delaySeconds));
                    }
                });

            return;
        }

        if ($rule->event == 'weekly_referral_reminder') {
            DB::table('customer_register as cr')
                ->join('referral_codes as rc', 'rc.user_id', '=', 'cr.id')
                ->select('cr.*', 'cr.id as id')
                ->where('cr.status', '0')
                ->where('cr.deletes', '0')
                ->where('rc.total_invites', 0)
                ->orderBy('id')
                ->chunkById(500, function ($users) use ($rule, $delaySeconds) {
                    foreach ($users as $user) {
                        ProcessAutomationPush::dispatch($rule->id, $user->id)
                            ->delay(now()->addSeconds($delaySeconds));
                    }
                }, 'id');

            return;
        }

        if ($rule->event == 'weekly_pool_awareness') {
            DB::table('customer_register as cr')
                ->select('cr.*', 'cr.id as id')
                ->where('cr.status', '0')
                ->where('cr.deletes', '0')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('cus_job_temp as ct')
                        ->whereColumn('ct.user_id', 'cr.id')
                        ->where('ct.global_type', 'carpool');
                })
                ->orderBy('id')
                ->chunkById(500, function ($users) use ($rule, $delaySeconds) {
                    foreach ($users as $user) {
                        ProcessAutomationPush::dispatch($rule->id, $user->id)
                            ->delay(now()->addSeconds($delaySeconds));
                    }
                }, 'id');

            return;
        }

        if ($rule->event == 'host_profile_reminder') {
            DB::table('customer_register as cr')
                ->select('cr.*', 'cr.id as id')
                ->where('cr.status', '0')
                ->where('cr.deletes', '0')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('cus_job_temp as ct')
                        ->whereColumn('ct.user_id', 'cr.id')
                        ->where('ct.global_type', 'carpool');
                })
                ->orderBy('id')
                ->chunkById(500, function ($users) use ($rule, $delaySeconds) {
                    foreach ($users as $user) {
                        ProcessAutomationPush::dispatch($rule->id, $user->id)
                            ->delay(now()->addSeconds($delaySeconds));
                    }
                }, 'id');

            return;
        }
    }
}