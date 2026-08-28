<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Model\RideFeedback;

class CustomerFeedback extends Controller
{
    public function submitFeedback(Request $request)
    {
        try {
            
            $request->validate([
                'job_id'           => 'required|exists:cus_job_temp,id',
                'rating'            => 'required|integer|min:1|max:5',
                'review'            => 'nullable|string|max:1000',
                'issue_type'        => 'nullable|string|max:100',
                'issue_description' => 'nullable|string|max:1000',
                'priority'          => 'nullable|in:normal,high',
                'tags'              => 'nullable|array'
            ]);
        
            $ride = DB::table('cus_job_temp')
                ->select('id','bids_details','user_id','job_status', 'assigned_to')
                ->where('id', $request->job_id)
                ->where('user_id', auth()->id())
                ->where('job_status', 'accept')
                ->first();
            // return $ride;
        
            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found'
                ], 422);
            }
            
            // return $ride;
        
            // Prevent duplicate feedback
            if (DB::table('customer_feedback')->where('job_id', $ride->id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Feedback already submitted'
                ], 422);
            }
        
            DB::beginTransaction();
        
        
            // $bids = json_decode($ride->bids_details, true);
    
            // if (!$bids || !is_array($bids)) {
            //     throw new \Exception('Invalid bid.');
            // }
    
            $driverId = null;
            $driverId = $ride->assigned_to;
    
            // foreach ($bids as $key => $bid) {
            //     if (isset($bid['status']) && $bid['status'] == 'accept') {
            //         $driverId = $key; // Key is driver ID
            //         break;
            //     }
            // }
    
            if (!$driverId) {
                throw new \Exception('Driver not found');
            }
    
            $priority = $request->priority ?? 'normal';
            $isFlagged = ($request->rating <= 2 || $priority === 'high') ? 1 : 0;
    
            DB::table('customer_feedback')->insert([
                'job_id'            => $ride->id,
                'job_no'            => $ride->job_no??null,
                'driver_id'         => $driverId,
                'customer_id'       => auth()->id(),
                'rating'            => $request->rating,
                'review'            => $request->review,
                'issue_type'        => $request->issue_type,
                'issue_description' => $request->issue_description,
                'priority'          => $priority,
                'tags'              => $request->tags ? json_encode($request->tags) : null,
                'is_flagged'        => $isFlagged,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
    
            // ✅ Update driver rating (incremental calculation)
            $driver = DB::table('user_register')
                ->select('id','total_ratings','ratings')
                ->where('id', $driverId)
                ->lockForUpdate()
                ->first();
    
            if (!$driver) {
                throw new \Exception('Driver not found');
            }
    
            $totalRatings = $driver->total_ratings ?? 0;
            $averageRating = $driver->ratings ?? 0;
    
            $newTotal = $totalRatings + 1;
    
            if ($totalRatings == 0) {
                $newAverage = $request->rating;
            } else {
                $newAverage = (
                    ($averageRating * $totalRatings) + $request->rating 
                ) / $newTotal;
            }
    
            DB::table('user_register')
                ->where('id', $driverId)
                ->update([
                    'ratings' => round($newAverage, 2),
                    'total_ratings'  => $newTotal
                    // 'ratings'  => $newTotal
                ]);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Feedback submitted successfully'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}