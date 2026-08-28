<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use DB;
use Config;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Collection;
use Carbon\Carbon;
class SEOMiddleware
{
    public function getCurrencySymbol($currencyCode)
    {
        $currencySymbols = [
            "INR" => "₹", // Indian Rupee
            "USD" => "$", // US Dollar
            "GBP" => "£", // British Pound
            "EUR" => "€", // Euro
            "JPY" => "¥", // Japanese Yen
            "AUD" => "A$", // Australian Dollar
            "CAD" => "C$", // Canadian Dollar
            "CHF" => "Fr", // Swiss Franc
            "CNY" => "¥", // Chinese Yuan
            "RUB" => "₽", // Russian Ruble
            // Add more currencies as needed
        ];
        return $currencySymbols[$currencyCode] ?? $currencyCode; // Return symbol or fallback to code
    }
    public function handle(Request $request, Closure $next)
    {
        $data = [];
        $data['getAllPages'] = null;
        $data['partnerWeb'] = null;
        $currentHost = request()->getHost() ?? '';
        $slug = $request->route('slug') ?: '';
        $bookingID = $request->route('bookingID') ?: '';
        if (empty($currentHost)) {
            return view('404.404');
        }
        $getDB = DB::connection('mysql1')->table('gentral_setting as g')
            ->join('partnerlists as p', 'g.partner_id', '=', 'p.id')
            ->where('g.cweburl', 'LIKE', $currentHost)
            ->where('g.status', '=', '0')
            ->orderBy('g.id', 'desc')
            ->limit(1)
            ->select('g.partner_id', 'p.*')
            ->first();
        if (!$getDB) {
            return view('404.404');
        }
        $database_name = $getDB->database_name ?? null;
        $database_user = $getDB->database_user ?? null;
        $database_password = $getDB->database_password ?? null;
        $db_host = $getDB->db_host ?? 'localhost';
        // dd($database_name);
        if (empty($database_name) || empty($database_user) || empty($database_password) || empty($db_host)) {
            return view('404.404');
        }
        // dd($database_user);
        Config::set('database.connections.partnerdb', [
            'driver' => 'mysql',
            'host' => $db_host,
            'database' => $database_name,
            'username' => $database_user,
            'password' => $database_password,
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]);
        DB::purge('partnerdb');
        DB::reconnect('partnerdb');
        $partnerData = DB::connection('partnerdb')->table('gentral_setting as g')
            ->where('g.cweburl', 'LIKE', $currentHost)
            // ->where('g.status', '=', '0')
            ->orderBy('g.id', 'desc')
            ->limit(1)
            ->first();
        if (!$partnerData) {
            return view('404.404');
        }
        // $data['partnerWeb' = null]
        //   $partnerData
        $data['partnerWeb'] = $partnerData;
        // dd($partnerData);
        if (isset($partnerData->id)) {
            $data['getAllPages'] = DB::connection('partnerdb')->table('articles')
                ->where('gentralID', '=', $partnerData->id)
                ->where('status', '=', 'active')
                ->orderByRaw('CAST(`order` AS SIGNED) ASC')
                ->get();
            $data['getAllPages'] = collect($data['getAllPages'])->sortBy('order')->values()->all();
            // dd($data['getAllPages']);
        }
        if ($slug == '' && isset($data['getAllPages'])) {

            if (collect($data['getAllPages'])->count() > 0) {
                // dd($data['getAllPages']);
                $topItems = collect($data['getAllPages'])->filter(function ($page) {
                    return $page->position == 'top';
                });
                if ($topItems->count() > 0) {
                    $slug = $topItems[0]->url ?? '';
                }
                // dd($slug);
                // $getFirstFive = collect($topItems)->take(5);
            }
        }
        // dd($slug);
        // dd($partnerData->id);
        $activePagesContent = null;
        // dd(isset($data['getAllPages']) && collect($data['getAllPages'])->count() > 0 && isset($slug));
        if (isset($data['getAllPages']) && collect($data['getAllPages'])->count() > 0 && isset($slug)) {
            $activePagesContent = collect($data['getAllPages'])->first(function ($record) use ($slug) {
                // dd($record->url);
                return isset($record->url) && $record->url === $slug;
            });
        }
        // dd($activePagesContent);
        // dd($activePagesContent);
        $data['partnerWeb']->title = $data['partnerWeb']->title ?? 'Go Ride';
        $data['partnerWeb']->meta_keyword = $data['partnerWeb']->meta_keyword ?? 'Go Ride';
        $data['partnerWeb']->meta_desp = $data['partnerWeb']->meta_desp ?? 'Go Ride';
        if (isset($activePagesContent)) {
            $data['partnerWeb']->title = $activePagesContent->meta_title ?? $data['partnerWeb']->title;
            $data['partnerWeb']->meta_keyword = $activePagesContent->meta_keyword ?? $data['partnerWeb']->meta_keyword;
            $data['partnerWeb']->meta_desp = $activePagesContent->meta_desp ?? $data['partnerWeb']->meta_desp;
        }
        $data['activePagesContent'] = $activePagesContent;
        $data['bookinfo'] = null;
        if (isset($bookingID) && $bookingID != null && $bookingID != '') {
            $bookSet = DB::connection('partnerdb')->table('bookingsetting')
                ->select('distance_unit', 'country', 'currency')
                ->where('partner_id', $getDB->partner_id)
                ->limit(1)
                ->first();
            // dd($bookSet, $partnerData->id);
            if (!$bookSet) {
                return redirect()->to("http://{$currentHost}/");
            }
            $data['bookinfo']['booking_distance_unit'] = ($bookSet->distance_unit == 'kms') ? '1000' : '1609.34';
            $data['bookinfo']['kmSymbol'] = ($bookSet->distance_unit == 'kms') ? 'Kms' : 'Miles';
            $data['bookinfo']['country'] = $bookSet->country ?? null;
            $data['bookinfo']['currency'] = $bookSet->currency ?? null;
            $currencySymbol = $data['bookinfo']['currencySymbol'] = $this->getCurrencySymbol($data['bookinfo']['currency']);
            $bookinfo = DB::connection('partnerdb')->table('bookinfo')
                    ->where('sid', $bookingID)
                    ->leftJoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
                    ->select(
                        'bookinfo.*',
                        DB::raw("COALESCE(driver.name, 'NULL') as driver_name"),
                        DB::raw("COALESCE(driver.driver_no, 'NULL') as driver_no"),
                        DB::raw("COALESCE(driver.vech_reg_num, 'NULL') as vech_reg_num"),
                        DB::raw("COALESCE(driver.phone, 'NULL') as driver_phone"),
                        DB::raw("COALESCE(driver.upload_photo, 'NULL') as driver_upload_photo")
                    )
                    ->first();

            if (!$bookinfo) {
                return redirect()->to("http://{$currentHost}/");
            }
            $carType = str_replace(' ', '_', strtolower($bookinfo->car_type)) ?? null;
            $data['bookinfo']['book'] = $bookinfo;
            $query = "SELECT " . ($carType) . " FROM `admin_form` WHERE " .
                ($bookinfo->from ? "area_from = '{$bookinfo->from}'" : "") .
                ($bookinfo->to ? " AND area_to = '{$bookinfo->to}'" : "") .
                ($bookinfo->viapoint1 ? " AND viapoint1 = '{$bookinfo->viapoint1}'" : "") .
                ($bookinfo->viapoint2 ? " AND viapoint2 = '{$bookinfo->viapoint2}'" : "") .
                ($bookinfo->viapoint3 ? " AND viapoint3 = '{$bookinfo->viapoint3}'" : "");
            $adminFromData = DB::connection('partnerdb')->selectOne($query);
            // // dd($adminFromData);
            $pickupTime = $bookinfo->pickup_time ?? null;
            $pickupDate = $bookinfo->pickup_date ?? null;
            $returnTime = null;
            $returnDate = null;
            $car_price_only = $adminFromData->{$carType} ?? $bookinfo->total;
            $pickupTimeCharge = 0;
            $returnTimeCharge = 0;
            $pickupDateCharge = 0;
            $returnDateCharge = 0;
            $pickupTimeContent = $returnTimeContent = $pickupDateContent = $returnDateContent = $restructionDuration = $returnRestructionDuration = '';
            if ($pickupTime != '') {
                $timeTwoDegit = substr($pickupTime, 0, 2);
                // dd($timeTwoDegit);
                $specialTimeCost = DB::connection('partnerdb')
                    ->table('special_time')
                    ->where('from', '<=', $timeTwoDegit)
                    ->where('to', '>=', $timeTwoDegit)
                    ->first();
                    if ($specialTimeCost) {
                        $pickupTimeCharge = floatVal($specialTimeCost->cost);
                        $message = " (Amount: {$currencySymbol}" . round($pickupTimeCharge) . ")";
                        $pickupTimeContent = $pickupTimeCharge ? ($specialTimeCost->content ? $specialTimeCost->content . $message : 'Special Time Charge Added') . $message : '';
                    }
                }
            //     if ($pickupTime != '') {
            //         $timeTwoDigit = intval(str_replace(':', '', $pickupTime));
            //         dd($specialTimeCost);
                    
            //         $specialTimeCost = DB::connection('partnerdb')
            //         ->table('special_time')
            //         ->select('cost', 'content', 'id')
            //         ->where('from', '<=', $timeTwoDigit)
            //         // ->where('to', '>=', $timeTwoDigit)
            //         ->first();
                    
            //         dd($specialTimeCost);
            //     if ($specialTimeCost) {
            //         $pickupTimeCharge = floatval($specialTimeCost->cost);
            //         $message = " (Amount: {$currencySymbol}" . round($pickupTimeCharge) . ")";
            //         $onlytimescost = $currencySymbol . round($pickupTimeCharge);
            //         $timecontentonly = $specialTimeCost->content ?? ''; // assuming 'content' is a column
            //         $onlytimescost = $onlytimescost ?: '';
            //     }
            // }
            

            if ($returnTime != '') {
                $timeTwoDegit = substr($returnTime, 0, 2);
                $specialTimeCost = DB::connection('partnerdb')
                    ->table('special_time')
                    ->where('from', '<=', $timeTwoDegit)
                    ->where('to', '>=', $timeTwoDegit)
                    ->first();
                if ($specialTimeCost) {
                    $returnTimeCharge = floatVal($specialTimeCost->cost);
                    $message = " ( Amount: {$currencySymbol}" . round($returnTimeCharge) . " )";
                    $returnTimeContent = $returnTimeCharge ? ($specialTimeCost->content ? $specialTimeCost->content . $message : 'Return Special Time Charge Added') . $message : null;
                }
            }
            if ($pickupDate != '') {
                $specialDateCost = DB::connection('partnerdb')
                ->table('special_price')
                ->where('dates', $pickupDate)
                ->first();
                // dd($pickupDate);
                if ($specialDateCost) {
                    $pickupDateCharge = floatVal($specialDateCost->cost);
                    $pickupDateCharge = $pickupDateCharge != 0 ? ($car_price_only * ($pickupDateCharge / 100)) : 0;
                    $specialCharge = $pickupDateCharge;
                    $message = " (Amount: {$currencySymbol}" . round($specialCharge) . ")";
                    $pickupDateContent = $pickupDateCharge ? ($specialDateCost->content ? $specialDateCost->content . $message : 'Special Date Charge Added ' . $message) : null;
                }
            }
            if ($returnDate != '') {
                $returnSpecialDateCost = DB::connection('partnerdb')
                    ->table('special_price')
                    ->where('dates', $returnDate)
                    ->first();
                if ($returnSpecialDateCost) {
                    $returnDateCharge = floatVal($returnSpecialDateCost->cost);
                    $returnDateCharge = $returnDateCharge != 0 ? ($car_price_only * ($returnDateCharge / 100)) : 0;
                    $specialCharge = $returnDateCharge;
                    $message = " (Amount: {$currencySymbol}" . round($specialCharge) . ")";
                    $returnDateContent = $returnDateCharge ? ($returnSpecialDateCost->content ? $returnSpecialDateCost->content . $message : 'Return Special Date Charge Added ' . $message) : null;
                }
            }
            if ($pickupTime && $pickupDate) {
                $combinedDT = Carbon::parse("$pickupDate $pickupTime")->format('Y-m-d H:i:s');
                $restrictionDate = DB::connection('partnerdb')
                    ->table('booking_restriction_setting')
                    ->where('from', '<=', $combinedDT)
                    ->where('to', '>=', $combinedDT)
                    ->first();
                $restructionDuration = $restrictionDate ? ('Booking Time not available : From ' . $restrictionDate->from . ' to ' . $restrictionDate->to) : '';
            }
            if ($returnTime && $returnDate) {
                $combinedDT = Carbon::parse("$returnDate $returnTime")->format('Y-m-d H:i:s');
                $returnRestrictionDate = DB::connection('partnerdb')
                    ->table('booking_restriction_setting')
                    ->where('from', '<=', $combinedDT)
                    ->where('to', '>=', $combinedDT)
                    ->first();
                $returnRestructionDuration = $returnRestrictionDate ? ('Booking Time not available : From ' . $returnRestrictionDate->from . ' to ' . $returnRestrictionDate->to) : '';
            }
            $data['bookinfo']['pickupTimeContent'] = $pickupTimeContent;
            $data['bookinfo']['pickupDateContent'] = $pickupDateContent;
            // $data['bookinfo']['onlytimescost'] = $onlytimescost;
        }
        // dd($data);
        view()->share('seoData', $data);
        return $next($request);
    }
}