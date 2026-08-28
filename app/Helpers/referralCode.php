<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class referralCode
{

    public static function generateReferralCode()
    {
        do {
            $now = Carbon::now();
    
            $year = $now->format('y');
    
            $month = $now->format('m');
    
            $prefix = strtoupper(Str::random(2));
    
            $suffix = strtoupper(Str::random(2));
    
            $code = $prefix . $year . $suffix . $month;
    
            $exists = DB::table('referral_codes')
                ->where('code', $code)
                ->exists();
    
        } while ($exists);
    
        return $code;
    }
}