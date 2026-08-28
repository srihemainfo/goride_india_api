<?php

namespace App\Http\Controllers\Api\v4;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DriverProfileScorer;

class DriverScoreController extends Controller
{
    public function updateAllDrivers()
    {
        $drivers = DB::table('user_register as ur')
            ->join('kyc_details as kd', 'kd.user_id', '=', 'ur.id')
            ->where('ur.deletes', '0')
            ->where('kd.type', 'Driver')
            ->select('ur.id')
            ->distinct()
            ->get();

        if ($drivers->isEmpty()) {
            return "No drivers found.";
        }

        $scorer = new DriverProfileScorer();
        $updatedCount = 0;

        foreach ($drivers as $driver) {
            $scorer->calculateAndUpdate($driver->id);
            $updatedCount++;
        }

        return "✅ Successfully updated {$updatedCount} drivers profile percentage.";
    }
    
    public function updateDriverScore(Request $request)
    {
    // Validate the incoming request
    $request->validate([
        'driver_id' => 'required|integer'
    ]);

    $id = $request->driver_id;

    // Check if the driver exists and is valid
    $driver = DB::table('user_register as ur')
        ->join('kyc_details as kd', 'kd.user_id', '=', 'ur.id')
        ->where('ur.id', $id)
        ->where('ur.deletes', '0')
        ->where('kd.type', 'Driver')
        ->select('ur.id')
        ->first();

    if (!$driver) {
        return response()->json([
            'status' => false, 
            'message' => 'Driver not found or invalid.'
        ], 404);
    }

    // Update the specific driver
    $scorer = new DriverProfileScorer();
    $scorer->calculateAndUpdate($driver->id);

    return response()->json([
        'status' => true, 
        'message' => "✅ Successfully updated profile percentage for driver ID: {$id}."
    ]);
}
}