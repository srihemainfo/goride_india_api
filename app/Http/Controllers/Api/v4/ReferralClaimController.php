<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralClaimController extends Controller
{
    // Configuration for the referral ranges based on your spreadsheet
    private $tiersConfig = [
        5  => ['start' => 1,  'end' => 5,  'amount' => 10.00], // 1st to 5th users
        10 => ['start' => 6,  'end' => 10, 'amount' => 15.00], // 6th to 10th users
        20 => ['start' => 11, 'end' => 20, 'amount' => 20.00], // 11th to 20th users
    ];

    public function checkEligibility(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid Bearer token.'
            ], 401);
        }
        
        $userId = $user->id; 

        $isCustomer = DB::table('referral_codes')
            ->where('user_id', $userId)
            ->where('app_name', 'customer')
            ->exists();

        if (!$isCustomer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer referral record not found.'
            ], 404);
        }

        // Count ONLY "completed" referrals
        $completedReferralsCount = DB::table('referrals')
            ->where('referrer_user_id', $userId)
            ->where('status', 'completed')
            ->count();

        // Fetch exact referral numbers the user has ALREADY claimed (e.g., [1, 2, 3, 6, 7])
        $claimedIndexes = DB::table('referral_claim_logs')
            ->where('user_id', $userId)
            ->pluck('referral_index')
            ->toArray();

        $tiersStatus = [];

        // Check each block to see if there are available individual claims
        foreach ($this->tiersConfig as $tierKey => $config) {
            $unclaimedCount = 0;
            $claimedCount = 0;

            // Loop through the specific user indexes for this tier (e.g., 6, 7, 8, 9, 10)
            for ($i = $config['start']; $i <= $config['end']; $i++) {
                if (in_array($i, $claimedIndexes)) {
                    $claimedCount++;
                } elseif ($i <= $completedReferralsCount) {
                    $unclaimedCount++; // Completed but not yet claimed!
                }
            }

            $isEligible = $unclaimedCount > 0;
            $isFullyClaimed = $claimedCount === ($config['end'] - $config['start'] + 1);
            
            // Amount currently waiting to be claimed (e.g., 15 Rs for 1 new user)
            $claimableAmount = $unclaimedCount * $config['amount'];
            
            // Total amount they have ALREADY claimed in this specific tier block
            $totalEarned = $claimedCount * $config['amount'];

            $tiersStatus[] = [
                'tier'             => $tierKey,
                'reward_amount'    => $claimableAmount, // Ready to claim right now
                'total_earned'     => $totalEarned,     // Already claimed and added to wallet
                'is_eligible'      => $isEligible, 
                'is_fully_claimed' => $isFullyClaimed    
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $tiersStatus
        ], 200);
    }

    public function claimThresholdReward(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid Bearer token.'
            ], 401);
        }

        $request->validate([
            'referral_index' => 'required|integer|in:5,10,20'
        ]);

        $userId = $user->id; 
        $tier = $request->referral_index;
        $config = $this->tiersConfig[$tier];

        try {
            // 1. Fetch referral data
            $referralData = DB::table('referral_codes')
                ->where('user_id', $userId)
                ->where('app_name', 'customer')
                ->first();

            if (!$referralData) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Customer referral record not found.'
                ], 404);
            }

            // 2. Count completed referrals
            $completedReferralsCount = DB::table('referrals')
                ->where('referrer_user_id', $userId)
                ->where('status', 'completed')
                ->count();

            // 3. Fetch already claimed indexes
            $claimedIndexes = DB::table('referral_claim_logs')
                ->where('user_id', $userId)
                ->pluck('referral_index')
                ->toArray();

            // 4. Find exactly which specific referrals are available to be claimed right now
            $indexesToClaim = [];
            $claimedInThisTier = 0;
            
            for ($i = $config['start']; $i <= $config['end']; $i++) {
                if (in_array($i, $claimedIndexes)) {
                    $claimedInThisTier++;
                } elseif ($i <= $completedReferralsCount) {
                    $indexesToClaim[] = $i; 
                }
            }

            // 5. Smart Messaging Check
            if (empty($indexesToClaim)) {
                $totalInTier = $config['end'] - $config['start'] + 1;
                
                if ($claimedInThisTier === $totalInTier) {
                    return response()->json(['success' => false, 'message' => "You have already fully claimed all rewards for the Tier $tier block."], 400);
                } elseif ($completedReferralsCount < $config['start']) {
                    $neededToStart = $config['start'] - $completedReferralsCount;
                    return response()->json(['success' => false, 'message' => "You currently have $completedReferralsCount completed referral(s). You need $neededToStart more to unlock the Tier $tier block."], 400);
                } else {
                    $nextTarget = $completedReferralsCount + 1;
                    return response()->json(['success' => false, 'message' => "You have claimed up to your current completed referrals. Complete referral #$nextTarget to unlock your next reward!"], 400);
                }
            }

            // 6. Calculate total payout amount
            $rewardAmount = count($indexesToClaim) * $config['amount'];

            // 7. FETCH OPENING BALANCE FOR WALLET HISTORY
            $customerRecord = DB::table('customer_register')->where('id', $userId)->first();
            $openingBalance = $customerRecord->walletBalance ?? 0;
            $closingBalance = $openingBalance + $rewardAmount;

            // ==========================================
            // START DATABASE TRANSACTION
            // ==========================================
            DB::beginTransaction();

            // A. Update the customer's exact wallet balance explicitly
            DB::table('customer_register')
                ->where('id', $userId)
                ->update(['walletBalance' => $closingBalance]);

            // B. Increment total lifetime rewards on the referral_codes table
            DB::table('referral_codes')
                ->where('id', $referralData->id)
                ->increment('total_rewards', $rewardAmount);

            // C. Log EACH specific referral index so they can never be claimed twice
            // We also collect the inserted IDs to reference them in the wallet history
            $claimLogIds = [];
            foreach ($indexesToClaim as $index) {
                $insertedId = DB::table('referral_claim_logs')->insertGetId([
                    'user_id' => $userId,
                    'referral_index' => $index,
                    'reward_amount' => $config['amount'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $claimLogIds[] = $insertedId;
            }

            DB::table('walletBalance_history')->insert([
                'uname'            => $user->name ?? 'User',
                // Fallback to 0 instead of '' because umobile is a bigint
                'global_type'      =>'customer',
                'umobile'          => $user->mobile ?? 0, 
                'uemail'           => $user->email ?? 'no-email', 
                'userid'           => $userId,
            
                'opening_balance'  => $openingBalance,
                'total'            => $rewardAmount,
                'closeing_balance' => $closingBalance, 
            
                'point_type'       => 'WALLET', 
                // Must perfectly match the ENUM in your database
                'transaction_type' => 'REFERRAL', 
                'reward_type'      => 'referral_threshold_bonus',
            
                // Reference the main referral_codes table ID since reference_id MUST be an integer
                'reference_id'     => $referralData->id, 
                'reference_table'  => 'referral_codes',
            
                'ip'               => request()->ip() ?? '127.0.0.1',
                'createdon'        => now(),
                'updatedon'        => now(),
            ]);

            DB::commit();
            // ==========================================
            // END DATABASE TRANSACTION
            // ==========================================

            return response()->json([
                'success' => true,
                'message' => 'Reward of ' . $rewardAmount . ' processed successfully for ' . count($indexesToClaim) . ' referral(s)!',
                'data' => [
                    'claimed_tier' => $tier,
                    'reward_amount' => $rewardAmount,
                    'claimed_referral_indexes' => $indexesToClaim,
                    'new_wallet_balance' => $closingBalance
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Referral Processing Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Server transaction failure during account processing.'
            ], 500);
        }
    }
}