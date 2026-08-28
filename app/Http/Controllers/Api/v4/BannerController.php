<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Fetch banners directly from the DB based on customer state, 
     * including the S3 image URL.
     */
    public function getBanners(Request $request)
    {
        // 1. Get the authenticated customer
        $customer = $request->user();

        // 2. Determine the target state (check if customer exists and has a state)
        $targetState = ($customer && !empty($customer->current_state)) 
                        ? $customer->current_state 
                        : null;

        // 3. Start building the query with a join to s3_images
        $query = DB::table('banner')
            ->leftJoin('s3_images', 'banner.image_id', '=', 's3_images.id')
            ->where('banner.is_active', 1); // Only active banners

        // 4. Apply the state logic
        if ($targetState) {
            // Customer has a state, match the display_state
            $query->where('banner.display_state', $targetState);
        } else {
            // Customer has NO state, fetch banners with no display_state
            $query->where(function ($q) {
                $q->whereNull('banner.display_state')
                  ->orWhere('banner.display_state', '');
            });
        }

        // 5. Select only the required columns and alias the image URL
        $banners = $query->select(
                'banner.id',
                'banner.title',
                'banner.subtitle',
                'banner.route',
                'banner.order_num',
                's3_images.s3_url as image_url'
            )
            ->orderBy('banner.order_num', 'asc')
            ->get();

        // 6. Return the clean JSON response
        return response()->json([
            'success' => true,
            'message' => 'Banners fetched successfully',
            'data' => $banners
        ], 200);
    }
}