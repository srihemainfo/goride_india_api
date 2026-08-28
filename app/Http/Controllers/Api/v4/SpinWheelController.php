<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpinWheelController extends Controller
{
    public function getOptions(Request $request)
    {
        $dbReward = DB::table('spin_wheel_rewards')
            ->where('reward_date', now()->format('Y-m-d'))
            ->where('status', 'active')
            ->first();

        if ($dbReward && $dbReward->rewards_data) {
            $options = json_decode($dbReward->rewards_data, true);
        } else {
            $options = [];
            for ($i = 1; $i <= 12; $i++) {
                $options[] = [
                    "slot"  => $i,
                    "type"  => "better_luck",
                    "limit" => 20,
                    "value" => null
                ];
            }
        }

        return response()->json([
            'status' => true,
            'data'   => $options
        ], 200);
    }
    
    public function spin(Request $request)
    {
    $userId = auth()->id() ?? $request->input('user_id');
    $todayDate = now()->format('Y-m-d');

    $lastSpin = DB::table('daily_spin_tracker')
        ->where('user_id', $userId)
        ->where('spin_date', $todayDate)
        ->orderBy('id', 'desc')
        ->first();

    if ($lastSpin) {
        $slotData = json_decode($lastSpin->slot_data, true);
        
        $currentSlot = isset($slotData[0]) ? end($slotData) : $slotData;

        if (!isset($currentSlot['type']) || $currentSlot['type'] !== 'free_spin') {
            return response()->json([
                'status' => false,
                'message' => 'You have already used your spin for today.'
            ], 403);
        }
    }

    $dbReward = DB::table('spin_wheel_rewards')
        ->where('reward_date', $todayDate)
        ->where('status', 'active')
        ->first();

    if (!$dbReward || !$dbReward->rewards_data) {
        return response()->json([
            'status' => false,
            'message' => 'Spin wheel is not active today.'
        ], 404);
    }

    $options = json_decode($dbReward->rewards_data, true);
    
    $availableOptions = array_filter($options, function($opt) {
        return $opt['limit'] > 0;
    });

    if (empty($availableOptions)) {
        $selectedSlot = [
            "slot" => 0,
            "type" => "better_luck",
            "limit" => 0,
            "value" => null
        ];
        $trackerRewardId = $dbReward->id;
    } else {
        $randomIndex = array_rand($availableOptions);
        $selectedSlot = $availableOptions[$randomIndex];

        foreach ($options as &$opt) {
            if ($opt['slot'] == $selectedSlot['slot']) {
                $opt['limit'] -= 1;
                break;
            }
        }

        DB::table('spin_wheel_rewards')
            ->where('id', $dbReward->id)
            ->update(['rewards_data' => json_encode(array_values($options))]);
            
        $trackerRewardId = $dbReward->id;
    }

    if ($lastSpin) {
        $existingSlots = json_decode($lastSpin->slot_data, true);
        
        if (!isset($existingSlots[0])) {
            $existingSlots = [$existingSlots];
        }
        
        $existingSlots[] = $selectedSlot;

        DB::table('daily_spin_tracker')
            ->where('id', $lastSpin->id)
            ->update([
                'reward_id' => $trackerRewardId,
                'slot'      => $selectedSlot['slot'] ?? 0,
                'slot_data' => json_encode($existingSlots),
                'spin_at'   => now()
            ]);
        $trackerId = $lastSpin->id;
    } else {
        $trackerId = DB::table('daily_spin_tracker')->insertGetId([
            'user_id'   => $userId,
            'reward_id' => $trackerRewardId,
            'slot'      => $selectedSlot['slot'] ?? 0,
            'slot_data' => json_encode([$selectedSlot]),
            'spin_date' => $todayDate,
            'spin_at'   => now()
        ]);
    }

    if ($selectedSlot['type'] === 'cashback' && $selectedSlot['value'] > 0) {
        $user = DB::table('user_register')->where('id', $userId)->first();
        
        if ($user) {
            $openingBalance = $user->walletBalance ?? 0.00;
            $bonusAmount = $selectedSlot['value'];
            $closingBalance = $openingBalance + $bonusAmount;

            DB::table('user_register')
                ->where('id', $userId)
                ->update(['walletBalance' => $closingBalance]);

            DB::table('walletBalance_history')->insert([
                'global_type'      => 'driver',
                'userid'           => $userId,
                'uname'            => $user->name ?? '',
                'umobile'          => $user->mobile ?? '',
                'uemail'           => $user->email ?? '',
                'opening_balance'  => $openingBalance,
                'bonus'            => 0,
                'total'            => $bonusAmount,
                'free_credits'     => 0,
                'closeing_balance' => $closingBalance,
                'point_type'       => 'WALLET',
                'transaction_type' => 'SPIN',
                'reward_type'      => 'spin_reward',
                'note'             => 'Spin Wheel Cashback',
                'reference_id'     => $trackerId,
                'reference_table'  => 'daily_spin_tracker',
                'ip'               => request()->ip(),
                'createdon'        => now(),
                'updatedon'        => now()
            ]);
        }
    }

    return response()->json([
        'status' => true,
        'data'   => $selectedSlot
    ], 200);
}
}