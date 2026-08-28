<?php

namespace App\Http\Controllers\Api\v5;

use Aws\S3\S3Client;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Services\NotificationService;


class MultipleVehicleController extends Controller
{
    public function store(Request $request)
    {
        try {
    
            $request->validate([
                'seater' => ['required'],
                'rc_number' => ['required'],
                'vehicle' => ['required', 'array'],
            ]);
    
            $userId = auth()->user()->id;
    
            $vehicleJson = null;
    
            if ($request->vehicle) {
                
                $vehicleJson = json_encode($request->vehicle);
            }
            $get_id = DB::table('kyc_details')
                ->where(['user_id' => $userId, 'deletes' => 0])
                ->select('id', 'type')
                ->first();
                
            if (!$get_id) {
                return response()->json([
                    'status' => false,
                    // 'data' => [],
                    'message' => 'KYC Pending.'
                ]);
            }
                
            if($get_id->type != 'Owner'){
                return response()->json([
                    'status' => false,
                    // 'data' => [],
                    'message' => 'Not eligible to upload multiple vehicle.'
                ]);
            }
    
            $exists = DB::table('owner_vehicle_list')
                ->where('user_id', $userId)
                ->where('deletes', 0)
                ->where('rc_number', $request->rc_number)
                ->exists();
    
            if (!$exists) {
    
                DB::table('owner_vehicle_list')->insert([
                    'user_id' => $userId,
                    'rc_number' => $request->rc_number,
                    'seater' => $request->seater,
                    'vehicle_details' => $vehicleJson,
                    'verification_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deletes' => 0,
                ]);
    
            } else {
    
                DB::table('owner_vehicle_list')
                    ->where('user_id', $userId)
                    ->where('rc_number', $request->rc_number)
                    ->where('deletes', 0)
                    ->update([
                        'seater' => $request->seater,
                        'vehicle_details' => $vehicleJson,
                        'verification_status' => 0, 
                        'updated_at' => now(),
                    ]);
            }
    
    
            
    
            $kycId = $get_id->id;
    
            $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
            $title = "Vehicle Details Updated — " . auth()->user()->name;
    
            $data = [
                'user_id' => $userId,
                'user_name' => auth()->user()->name,
                'kyc_id' => $kycId,
                'status' => 'Inreview',
                'changes' => null,
            ];
    
            // NotificationService::create('kyc.updated', $title, $data, $link, $userId);
    
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Vehicle Uploaded.'
            ]);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}