<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DriverProfileScorer
{
    // public function calculateAndUpdate($driverId)
    // {
    //     $driver = DB::table('user_register')
    //         ->where('id', $driverId)
    //         ->first();

    //     $kyc = DB::table('kyc_details')
    //         ->where('user_id', $driverId)
    //         ->where('type', 'Driver')
    //         ->first();

    //     if (!$driver) {
    //         return 0;
    //     }

    //     $score = 0;

    //     if ($driver->doc_verify == 1 && $driver->vehicle_verify == 2) {
    //         $score += 40;
    //     }

    //     if (!empty($driver->state)) $score += 3;
    //     if (!empty($driver->districts_id)) $score += 3;

    //     $imagesCount = 0;

    //     if (!empty($driver->profile_img_url)) $imagesCount++;
    //     if (!empty($driver->licence_image)) $imagesCount++;
    //     if (!empty($driver->aadhar_image_front)) $imagesCount++;
    //     if (!empty($driver->aadhar_image_back)) $imagesCount++;

    //     if ($imagesCount >= 4) {
    //         $score += 5;
    //     }

    //     if (!empty($kyc->dl_expiry)) $score += 4;
    //     if (!empty($driver->passport_expiry)) $score += 4;

    //     if (!empty($kyc->vehicle_type)) $score += 3;
    //     if (!empty($driver->fuel_type)) $score += 3;
    //     if (!empty($kyc->exp)) $score += 3;
    //     if (!empty($driver->seaters)) $score += 3;

    //     if ($driver->per_km > 0) $score += 2;
    //     if ($driver->extra_per_km > 0) $score += 2;
    //     if ($driver->per_hour > 0) $score += 2;
    //     if ($driver->per_day > 0) $score += 2;

    //     if (!empty($driver->upiID)) $score += 2;
    //     if (!empty($driver->reviews)) $score += 2;

    //     if ($score > 100) {
    //         $score = 100;
    //     }
        
    //     DB::table('user_register')
    //         ->where('id', $driverId)
    //         ->update([
    //             'profile_percentage' => $score
    //         ]);

    //     return $score;
    // }
    
   public function calculateAndUpdate($driverId)
   {
    // 1. Fetch User Data
    $driver = DB::table('user_register')
        ->where('id', $driverId)
        ->first();

    // 2. Fetch KYC Data
    $kyc = DB::table('kyc_details')
        ->where('user_id', $driverId)
        ->where('type', 'Driver')
        ->first();

    // 3. Fetch Schedule Data
    // 1. Get all the JSON strings for this driver
    $schedules = DB::table('schedule_dates')
        ->where('user_id', $driverId)
        ->where('deletes', '0')
        ->pluck('dates_price');
    
    $scheduleCount = 0;
    
    // 2. Loop through each row, decode the JSON, and add to the total count
    foreach ($schedules as $jsonDates) {
        $datesArray = json_decode($jsonDates, true);
        if (is_array($datesArray)) {
            $scheduleCount += count($datesArray);
        }
    }

    if (!$driver) {
        return 0;
    }

    $score = 0;
    
    // Safely decode the vehicle details JSON
    $v_details = json_decode($driver->vehicle_details ?? '{}', true);
    if (!is_array($v_details)) {
        $v_details = [];
    }

    // --------------------------------------------------------
    // SCORING LOGIC (Max 100)
    // --------------------------------------------------------

    // 1. Complete verified (40 Points)
    if ($driver->doc_verify == 1 && $driver->vehicle_verify == 2) {
        $score += 40;
    }

    // 2. Make & Model (4 Points)
    $makerModel = $driver->maker_model ?? ($v_details['rc_details']['response']['vehicle_details']['maker_model'] ?? '');
    if (!empty($makerModel)) {
        $score += 4; // Gives full 4 points if the maker/model string exists
    }

    // 3. Schedule 5 Location (5 Points)
    if ($scheduleCount >= 5) {
        $score += 5;
    }

    // 4. State (2 Points)
    if (!empty($driver->state)) $score += 2;
    
    // 5. District (2 Points)
    if (!empty($driver->districts_id)) $score += 2;

    // ==========================================
    // HELPER: Strictly validate real images
    // ==========================================
    $isValidImage = function($img) {
        if (empty($img) || $img === '0' || strtolower($img) === 'null') return false;
        if (strpos($img, 'data:image/svg') !== false) return false; // Blocks the 'No Image' SVG
        if (strpos($img, 'assets/images/') !== false) return false; // Blocks placeholders
        return true;
    };

    // 6. Above 5 Images (5 Points)
    $imagesCount = 0;
    $imageFields = [
        $driver->img_url ?? null,
        $driver->profile_img_url ?? null,
        $driver->licence_image ?? null,
        $driver->aadhar_image_front ?? null,
        $driver->aadhar_image_back ?? null,
        $driver->idProFront ?? null,
        $driver->idProBack ?? null,
        $kyc->front ?? null,
        $kyc->back ?? null,
        $v_details['vehicle']['front_view_image_url'] ?? null,
        $v_details['vehicle']['back_view_image_url'] ?? null,
        $v_details['vehicle']['side_view_image_url'] ?? null,
        $v_details['vehicle']['interior_front_image_url'] ?? null,
        $v_details['vehicle']['interior_rear_image_url'] ?? null,
        $v_details['vehicle']['boot_image_url'] ?? null,
        $v_details['vehicle']['special_features_image_url'] ?? null,
        $v_details['rc_front_image_url'] ?? null,
        $v_details['rc_back_image_url'] ?? null,
        $v_details['puc_details']['puc_image_url'] ?? null,
        $v_details['insurance_details']['insurance_image_url'] ?? null
    ];
    
    // Count only valid images
    foreach ($imageFields as $img) {
        if ($isValidImage($img)) {
            $imagesCount++;
        }
    }
    
    if ($imagesCount >= 5) {
        $score += 5;
    }

    // 7. PUC Certificate & Expiry (3 Points)
    $puc_date = $driver->puc_upto ?? ($v_details['puc_details']['puc_exp_date'] ?? '');
    $puc_img = $v_details['puc_details']['puc_image_url'] ?? '';
    if (!empty($puc_date) && $isValidImage($puc_img)) {
        $score += 3;
    }

    // 8. Driving Licence Expiry (3 Points)
    $dlExpiry = $kyc->dl_expiry ?? ($driver->li_upto ?? ($driver->dl_expiry ?? ($driver->passport_expiry ?? '')));
    if (!empty($dlExpiry)) $score += 3;

    // 9. Insurance & Expiry (3 Points)
    $ins_date = $driver->insurance_upto ?? ($v_details['insurance_details']['insurance_exp_date'] ?? '');
    $ins_img = $v_details['insurance_details']['insurance_image_url'] ?? '';
    if (!empty($ins_date) && $isValidImage($ins_img)) {
        $score += 3;
    }

    // 10. FC/RC Images & Expiry (3 Points)
    $fc = $driver->rc_upto ?? ($v_details['rc_expiry_date'] ?? ($v_details['rc_details']['response']['vehicle_details']['fit_up_to'] ?? ''));
    $rc_front = $v_details['rc_front_image_url'] ?? '';
    $rc_back = $v_details['rc_back_image_url'] ?? '';
    if (!empty($fc) && $isValidImage($rc_front) && $isValidImage($rc_back)) {
        $score += 3;
    }

    // 11. Vehicle Type (2 Points)
    $vType = $v_details['type'] ?? ($driver->cab_type ?? ($kyc->cab_type ?? ''));
    if (!empty($vType)) $score += 2;

    // 12. Fuel Type (2 Points)
    $fuel = $driver->fuel_type ?? ($driver->fuel_types ?? ($v_details['vehicle_questions']['fuel_type'] ?? ($v_details['rc_details']['response']['vehicle_details']['fuel_type'] ?? '')));
    if (!empty($fuel)) $score += 2;

    // 13. Driver Experience (2 Points)
    $experience = $kyc->exp ?? ($driver->exp ?? '');
    if (!empty($experience)) $score += 2;

    // 14. Seat Capacity (3 Points)
    $seats = $driver->seat ?? ($driver->seaters ?? ($v_details['rc_details']['response']['vehicle_details']['seat_capacity'] ?? ($kyc->seater ?? '')));
    if (!empty($seats)) $score += 3;

    // 15. Luggage Capacity (3 Points)
    $luggage = $driver->Luggage ?? ($driver->luggage ?? ($v_details['user_info']['luggage'] ?? ''));
    if (!empty($luggage)) $score += 3;

    // 16. Language Known (2 Points)
    $lang = $driver->language ?? ($v_details['user_info']['language'] ?? '');
    if (!empty($lang)) $score += 2;

    // 17. Price Per KM (2 Points)
    if (isset($driver->per_km) && $driver->per_km > 0) $score += 2;

    // 18. Extra Price Per KM (3 Points - includes +1 from Hill)
    $extraKm = $driver->extra_per_km ?? ($driver->extra_price_per_km ?? 0);
    if ($extraKm > 0) $score += 3;

    // 19. Extra Price Per Hour (3 Points - includes +1 from Hill)
    $extraHr = $driver->per_hour ?? ($driver->price_per_hour ?? 0);
    if ($extraHr > 0) $score += 3;

    // 20. Extra Price Per Day (2 Points)
    $extraDay = $driver->per_day ?? ($driver->price_per_day ?? 0);
    if ($extraDay > 0) $score += 2;

    // 21. UPI ID (2 Points)
    $upi = $driver->upiID ?? ($driver->upi_id ?? '');
    if (!empty($upi)) $score += 2;

    // 22. Reviews (2 Points)
    if (!empty($driver->reviews)) $score += 2;

    // 23. Remarks (2 Points)
    if (!empty($driver->remarks)) $score += 2;

    // --------------------------------------------------------
    // FINALIZE & SAVE
    // --------------------------------------------------------

    // Cap at 100 max
    if ($score > 100) {
        $score = 100;
    }

    DB::table('user_register')
        ->where('id', $driverId)
        ->update([
            'profile_percentage' => $score
        ]);

    return $score;
}
}