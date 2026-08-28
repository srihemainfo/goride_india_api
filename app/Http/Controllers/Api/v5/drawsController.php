<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Http\Controllers\Api\v5\billingController;
use App\Models\user_register;
use Illuminate\Support\Facades\Auth;


class drawsController extends Controller
{

  // get Active Draw - PRAKASH 16-9-2023
  public function getActiveDraw(Request $request)
  {

    try {
      $response = [];

      $datas = [];
      $request->article = Controller::BlockSQLInjection($request->article);
      $article = (isset($request->article) && $request->article != '') ? $request->article : 'home';


      // SEO
      $datas['seoTags']['id'] =  '';
      $datas['seoTags']['metaTitle'] =  '';
      $datas['seoTags']['metaDes'] = '';
      $datas['seoTags']['metaKeyword'] = '';
      $datas['seoTags']['canonical'] = '';
      if ($article) {
        $art_pages = DB::table('article')
          ->where('url', '=', $article)
          ->where('status', '=', 'Active')
          ->orderby('id', 'desc')
          ->limit(1)
          ->get();

        if ($art_pages->count() > 0) {
          $datas['seoTags']['id'] =  $art_pages[0]->id;
          $datas['seoTags']['metaTitle'] =   $art_pages[0]->meta_title;
          $datas['seoTags']['metaDes'] = $art_pages[0]->meta_desp;
          $datas['seoTags']['metaKeyword'] = $art_pages[0]->meta_keyword;
        }
      }


      // Home Banners
      $homeBanner = DB::table('home_banner')->where('start_date', '<', now())
        ->where('end_date', '>', now())
        ->select('id', 'device_type', DB::raw("CONCAT('" . env('DO_REDIRECT_URL') . "', path) AS path"), 'url_link')
        ->where('status', 'Active')
        ->where('deletes', '0')
        ->orderBy('order_by', 'ASC')
        ->get();


      $mobBanner = [];
      $deskBanner = [];

      if ($homeBanner->count() > 0) {
        foreach ($homeBanner as $banner) {
          $id = $banner->id;
          if ($banner->device_type == 'DESKTOP') {
            $deskBanner[] = [
              'id' => $banner->id,
              'imageURL' => $banner->path,
              'redirectURL' => $banner->url_link ?? ''
            ];
          } else if ($banner->device_type == 'MOBILE') {
            $mobBanner[] = [
              'id' => $banner->id,
              'imageURL' => $banner->path,
              'redirectURL' => $banner->url_link ?? ''
            ];
          }
        }
      }

      $datas['homeBanners'] = [
        'DESKTOP' => $deskBanner,
        'MOBILE' => $mobBanner,
      ];


      $settingsWhere = [];

      if (isset($request->mainDomain)) {
        $settingsWhere['mainDomain'] =  $request->mainDomain;
      } else {
        $settingsWhere['id'] = 1;
      }

      // Settings 
      $settings = DB::table('settings')
        ->where($settingsWhere)
        ->limit(1)
        ->get();


      if ($settings->count() > 0) {
        $settings[0]->googletagmanager = json_decode($settings[0]->googletagmanager);

        $datas['settings'] = $settings[0];
      }

      // Testimonials
      $collectTestimonials = DB::table('testimonials')
        ->select('id', 'userid', 'name', 'name', DB::raw("CONCAT('" . env('DO_REDIRECT_URL') . "', thumbnail) AS thumbnail"), 'prize')
        ->where(['states' => '0', 'deletes' => '0'])
        ->orderBy('orderNo', 'ASC')
        ->get();

      $testimonials = [];

      if ($collectTestimonials->count() > 0) {
        $testimonials = $collectTestimonials;
      }

      $datas['testimonials'] = $testimonials;

      /// NEW 30 Day Plan Concept
      $currentActiveDraw = DB::table('draw')
        ->where([
          ['saleDate', '>=', ((date('G') >= 19) ? date('Y-m-d', strtotime('+1 day')) : date('Y-m-d'))],
          ['deletes', '=', '0'],
          ['dailyThirllStatus', '=', 'Active']
        ])
        ->orderBy('saleDate', 'ASC')
        ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'startDate', 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
        ->limit(1)
        ->get();



      $displayDrawDate = DB::table('draw')
        ->where([
          // ['resultDate', '>=', ((date('G') >= 19) ? date('Y-m-d', strtotime('+1 day')) : date('Y-m-d'))],
          ['startDate', '<=', ($currentActiveDraw->count() > 0 ? date('Y-m-d', strtotime($currentActiveDraw[0]->startDate))  : date('Y-m-d'))],
          ['deletes', '=', '0'],
          ['dailyThirllStatus', '=', 'Active']
        ])
        ->orderBy('resultDate', 'DESC')
        // ->orderBy('id', 'DESC')
        ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'startDate', 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
        ->limit(1)
        ->get();



      // Original Active Draw
      $activeDraw = DB::table('draw')
        ->where([
          ['saleDate', '>=', date('Y-m-d')],
          ['deletes', '=', '0'],
          ['dailyThirllStatus', '=', 'Active']
        ])
        ->orderBy('saleDate', 'ASC')
        ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'startDate', 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
        ->limit(1)
        ->get();

      // dd($activeDraw);

      $lastDraw = DB::table('draw')
        ->where([
          ['deletes', '=', '0'],
          // ['dailyThirllStatus', 'IN', 'Active', 'Completed']
        ])
        ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
        ->orderBy('saleDate', 'DESC')
        ->select('id', 'startDate', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'salesStrategyFormula', 'todayGoldPrize', 'previewData', 'deletes')
        ->limit(1)
        ->get();


      $datas['displayDraw'] = [];
      if ($displayDrawDate->count() > 0) {
        $datas['displayDraw'] = $displayDrawDate[0];
        $datas['displayDraw']->salesStrategyFormula = json_decode($displayDrawDate[0]->salesStrategyFormula, true);
      }


      $productListCollect = DB::table('product')
        ->where([
          ['deletes', '=', '0'],
          // ['type', '=', 'PRODUCT']
        ])
        ->whereNotIn('id', [1, 2])
        ->whereIn('type', ['PRODUCT', 'RENEWAL'])
        ->orderBy('id', 'ASC')
        ->select(DB::raw('CAST(eligibleDraw AS JSON) AS eligibleDraw'), 'qty', 'id', 'name', 'raffleQuantity', DB::raw("CONCAT('" . env('DO_REDIRECT_URL') . "', image) AS image"), 'rate', 'validityDays', 'type', 'description', 'chances', 'maxPrize', 'eligibleDrawCount', 'MRP')
        ->get();

      $datas['totalDays']['Active'] = 0;
      $datas['totalDays']['Inactive'] = 360;

      $datas['activeDraw'] = [];
      if ($activeDraw->count() > 0 && $lastDraw->count() > 0) {

        // Ticket Selling Concept 
        $datas['activeDraw'] = $activeDraw[0];
        $datas['activeDraw']->salesStrategyFormula = json_decode($activeDraw[0]->salesStrategyFormula);

        $specificDatess = date('Y-m-d', strtotime($lastDraw[0]->saleDate));

        $daysDifferenceCompleted = Carbon::parse($activeDraw[0]->saleDate)->diffInDays(Carbon::parse($specificDatess)) + 1;

        // $daysDifferenceCompleted = Carbon::parse($currentActiveDraw[0]->saleDate)->diffInDays(Carbon::parse($specificDatess)) + 1;

        $datas['totalDays']['Active'] = $daysDifferenceCompleted;
        $datas['totalDays']['Inactive'] = 360 - $daysDifferenceCompleted;

        // Product List
        $productList = [];

        if ($productListCollect && $productListCollect->count() > 0) {
          // NEW
          $productList = collect($productListCollect)->map(function ($item) use ($lastDraw, $activeDraw, $currentActiveDraw) {
            $item->eligibleDraw =  json_decode($item->eligibleDraw, true);
            $item->name = $item->qty . ' ' . $item->name;

            if (in_array($item->id, [4])) {
              $specificDate  = date('Y-m-d', strtotime($lastDraw[0]->saleDate));
              $daysDifference = Carbon::parse($activeDraw[0]->saleDate)->diffInDays(Carbon::parse($specificDate)) + 1;

              $endDate = Carbon::parse(date('Y-m-d', strtotime($activeDraw[0]->saleDate)))->addDays($daysDifference)->format('Y-m-d');

              // dd($endDate);
            } else {
              // $endDate = Carbon::parse($activeDraw[0]->saleDate)->addDays($item->validityDays)->format('Y-m-d');

              $endDate = Carbon::parse($activeDraw[0]->startDate)->addDays($item->validityDays)->format('Y-m-d');
            }

            // dd($endDate);
            $maxPrizeSum = 0;

            // $item->maxPrize = 0;
            $noOfDraw = 0;
            if ($item->eligibleDraw['is_thrill']) {
              // $thirllDraw = DB::table('draw')
              //   ->where([
              //     ['resultDate', '>', ($activeDraw->count() > 0 ? date('Y-m-d', strtotime($activeDraw[0]->startDate))  : date('Y-m-d'))],
              //     ['resultDate', '<=', $endDate],
              //     ['deletes', '=', '0'],
              //     ['dailyThirllStatus', '=', 'Active']
              //   ])
              //   ->orderBy('resultDate', 'ASC')
              //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
              //   ->count();

              $thirllDraw = DB::table('draw')
                ->where([
                  ['resultDate', '>', ($activeDraw->count() > 0 ? date('Y-m-d', strtotime($activeDraw[0]->startDate)) : date('Y-m-d'))],
                  ['resultDate', '<=', $endDate],
                  ['deletes', '=', '0'],
                  ['dailyThirllStatus', '=', 'Active']
                ])
                ->select(
                  // DB::raw('SUM(dailyThirllPrice) as totalPrice'),
                  // DB::raw('COUNT(*) as totalRecords')
                  DB::raw('COALESCE(SUM(dailyThirllPrice), 0) as totalPrice'),
                  DB::raw('COALESCE(COUNT(*), 0) as totalRecords')
                )
                ->first();

              if (in_array($item->id, [3, 4])) {
                $item->validityDays = $thirllDraw->totalRecords;
              }



              $noOfDraw += $thirllDraw->totalRecords;

              $maxPrizeSum += $thirllDraw->totalPrice;
            }

            if ($item->eligibleDraw['is_weekly']) {
              // $noOfDraw  += DB::table('draw')
              //   ->where([
              //     ['resultDate', '>', ($activeDraw->count() > 0 ? date('Y-m-d', strtotime($activeDraw[0]->startDate))  : date('Y-m-d'))],
              //     ['resultDate', '<=', $endDate],
              //     ['deletes', '=', '0'],
              //     ['weeklyBoosterStatus', '=', 'Active']
              //   ])
              //   ->orderBy('resultDate', 'ASC')
              //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
              //   ->count();

              $weeklyDraw = DB::table('draw')
                ->where([
                  ['resultDate', '>', ($activeDraw->count() > 0 ? date('Y-m-d', strtotime($activeDraw[0]->startDate))  : date('Y-m-d'))],
                  ['resultDate', '<=', $endDate],
                  ['deletes', '=', '0'],
                  ['weeklyBoosterStatus', '=', 'Active']
                ])
                ->orderBy('resultDate', 'ASC')
                ->select(
                  // DB::raw('SUM(weeklyBoosterPrice) as totalPrice'),
                  // DB::raw('COUNT(*) as totalRecords')

                  DB::raw('COALESCE(SUM(weeklyBoosterPrice), 0) as totalPrice'),
                  DB::raw('COALESCE(COUNT(*), 0) as totalRecords')
                )
                ->first();
              // ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
              // ->count();

              // dd($weeklyDraw);
              $noOfDraw += $weeklyDraw->totalRecords;

              $maxPrizeSum += $weeklyDraw->totalPrice;
            }

            if ($item->eligibleDraw['is_bumper']) {

              // $noOfDraw  += DB::table('draw')
              //   ->where([
              //     ['resultDate', '>', ($activeDraw->count() > 0 ? date('Y-m-d', strtotime($activeDraw[0]->startDate))  : date('Y-m-d'))],
              //     ['resultDate', '<=', $endDate],
              //     ['deletes', '=', '0'],
              //     ['monthlyBumperStatus', '=', 'Active'],
              //     ['monthlyBumperPrice', '<=', $item->maxPrize]
              //   ])
              //   ->orderBy('resultDate', 'ASC')
              //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
              //   ->count();

              $bumperDraw = DB::table('draw')
                ->where([
                  ['resultDate', '>', ($activeDraw->count() > 0 ? date('Y-m-d', strtotime($activeDraw[0]->startDate))  : date('Y-m-d'))],
                  ['resultDate', '<=', $endDate],
                  ['deletes', '=', '0'],
                  ['monthlyBumperStatus', '=', 'Active'],
                  ['monthlyBumperPrice', '<=', $item->maxPrize]
                ])
                ->orderBy('resultDate', 'ASC')
                ->select(
                  // DB::raw('SUM(monthlyBumperPrice) as totalPrice'),
                  // DB::raw('COUNT(*) as totalRecords')

                  DB::raw('COALESCE(SUM(monthlyBumperPrice), 0) as totalPrice'),
                  DB::raw('COALESCE(COUNT(*), 0) as totalRecords')
                )
                ->first();
              // ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
              // ->count();


              $noOfDraw += $bumperDraw->totalRecords;

              $maxPrizeSum += $bumperDraw->totalPrice;
            }

            $item->maxPrize = strval($maxPrizeSum);
            $item->weightTxt = 'G';


            if (in_array($item->id, [4])) {
              // $maxPrizeSum = 10400;
              $item->maxPrize = strval(round($maxPrizeSum / 1000));
              $item->weightTxt = 'KG';
            }



            $item->eligibleDrawCount =  $noOfDraw;
            $item->chances = $item->eligibleDrawCount * $item->raffleQuantity;

            $item->discountAmt = 0;
            $item->discountID = null;
            $txTime = date('Y-m-d H:i:s');

            $discountLike = DB::table('discount_periods')
              ->where('type', '=', 'general')
              ->where('start_date', '<', $txTime)
              ->where('end_date', '>', $txTime)
              ->where('deletes', '0')
              ->where('discount_amount', '>', 0)
              ->where('product_id', '=',  $item->id)
              ->orderBy('id', 'DESC')
              ->limit(1)
              ->get();

            if ($discountLike->count() > 0) {
              $item->discountAmt = floatval($discountLike[0]->discount_amount);

              $item->discountRate = floatval($item->rate -  $discountLike[0]->discount_amount);

              $item->discountID = $discountLike[0]->id;
            }


            $item->stackStatus = ($item->id === 1 || $item->id === 2) ?  false : true;


// Temp
                $item->rate = $item->id == 3  ? 15 : 75;
                $item->image =  $item->id == 4  ? 'https://nationalasset.blr1.digitaloceanspaces.com/nationaldraw/1/PROLL4.png' : $item->image;
                

            return $item;
          })->all();
        }

        $datas['productList'] = $productList;
      } else {
        if ($productListCollect && $productListCollect->count() > 0) {
          $datas['productList'] = collect($productListCollect)->map(function ($item) use ($lastDraw, $activeDraw) {
            $item->eligibleDraw =  json_decode($item->eligibleDraw, true);
            $item->name = $item->qty . ' ' . $item->name;

            $item->stackStatus = ($item->id === 1 || $item->id === 2) ?  false : true;


            return $item;
          })->all();
        }
      }



      $datas['homeBottomBanner'] = [
        'totalChances' => null,
        'drawCount' => null,
        'noOfDays' => null,
      ];

      $user = user_register::first();
      if ($user) {
        Auth::login($user);
        // Now the first user is authenticated

        // User::first()
        $productSrcDetails = new Request([
          'cartDetails' => [
            'productID' => 4,
            'quantity' => 1
          ],
          'purchaseStatus' => 'NEW',
          'pageTitle' => 'productDetails'
        ]);
        $billingCon = new billingController();
        $aunnalRes = $billingCon->addToCardProduct($productSrcDetails);
        $anuData = json_decode($aunnalRes->getContent(), true);

        if ($anuData['status'] === 'success') {
          $datas['homeBottomBanner']['totalChances'] = $anuData['data']['cartData']['totalChances'];
          $datas['homeBottomBanner']['noOfDays'] = $anuData['data']['cartData']['noOfDays'];

          $datas['homeBottomBanner']['drawCount'] = $anuData['data']['cartData']['drawCount']['thrillCount'] + $anuData['data']['cartData']['drawCount']['boosterCount'] + $anuData['data']['cartData']['drawCount']['bumberCount'];
        }
        // dd($anuData);
      }







      $response = ['status' => 'success', 'message' => 'Active Draw Details Collected!', 'data' => $datas];
      goto returnFVI;


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process Failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
