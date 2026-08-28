<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FamousPlaceController extends Controller
{

    public function categories(Request $request)
    {
        $categories = DB::table('famous_spots_categories')
            ->select(
                'id',
                'category_name',
                'icon_url'
            )
            ->where('status', 0)
            ->where('deleted', 0)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories,
        ]);
    }

    public function places(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);

        }

        $places = DB::table('famous_spots_locations as fsl')
            ->leftJoin(
                'famous_spots_categories as fsc',
                'fsc.id',
                '=',
                'fsl.category_id'
            )
            ->select(
                'fsl.id',
                'fsl.category_id',
                'fsc.category_name',
                'fsl.place_id',
                // 'fsl.location_name',
                'fsl.display_name',
                'fsl.image_url'
            )
            ->where('fsl.category_id', $request->category_id)
            ->where('fsl.status', 0)
            ->where('fsl.deleted', 0)
            ->orderBy('fsl.id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Places fetched successfully',
            'data' => $places,
        ]);
    }
}