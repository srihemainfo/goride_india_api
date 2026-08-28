<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class FeedBackController extends Controller
{
    public function saveFeedback(Request $request)
    {
    try {
        $userId = auth()->id();

        $existing = DB::table('user_feedbacks')
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if (!is_null($existing->rating)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Feedback already provided.'
                ], 400);
            }

            if ($request->boolean('is_dismissed') && !is_null($existing->dismissed_date)) {
                if (date('Y-m-d', strtotime($existing->dismissed_date)) === date('Y-m-d')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Feedback already dismissed today.'
                    ], 400);
                }
            }
        }

        $updateData = [
            'global_type' => $request->input('global_type', 'driver'),
        ];

        if ($request->boolean('is_dismissed')) {
            $updateData['dismissed_date'] = now();
        } else {
            $updateData['rating'] = $request->input('rating');
            $updateData['feedback_text'] = $request->input('feedback_text');
        }

        if ($existing) {
            DB::table('user_feedbacks')
                ->where('id', $existing->id)
                ->update($updateData);
        } else {
            $updateData['user_id'] = $userId;
            $updateData['created_at'] = now();

            DB::table('user_feedbacks')->insert($updateData);
        }

        return response()->json([
            'status' => true,
            'message' => 'Feedback saved successfully.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}