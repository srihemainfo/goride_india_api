<?php

namespace App\Services\Reports;

use App\Services\Reports\ReportsHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel, SheetCollection};

class ExcelExportService
{
    private $helper_service;

    public function __construct()
    {
        $this->helper_service = new ReportsHelperService;
    }

    public function admin_daily_export(Request $request)
    {
        $date = Carbon::parse($request->date_filter)->format('Y-m-d');
        $file_name = 'admin_day_report_';

// \DB::enableQueryLog();
        $job_details = DB::table('bookinfo')
            ->select('bookinfo.*',
            'driver.id as d_id',
            'name'
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->where('pickup_date', '=', $date)
            ->get();
            // dd(\DB::getQueryLog());

        if ($job_details->isNotEmpty()) {
            return (new FastExcel($job_details))
                ->download($file_name . $date . '.xlsx', function ($job) {
                    // return [
                    //     'Driver Name' => ucwords(strtolower($job->driver_name)),
                    //     'Job No' => $job->job_no,
                    //     'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
                    //     'From' => $job->from,
                    //     'To' => $job->to,
                    //     'Car Type' => $job->car_type,
                    //     'Total Amount' => $job->total,
                    //     'Order Status' => $job->order_status,
                    // ];
                    
                    $arr['Job No'] = $job->job_no;
                    $clients = DB::table('account')->where('email','=',$job->email)->get();
                    foreach($clients as $key => $client){
                        $arr['Client Name'] = $client->f_name;
                        $arr['Client Email'] = $client->email;
                        $arr['Client Mobile'] = $client->phone;
                    }
                    $arr['Pickup time'] = $job->pickup_time;
                    $arr['Job Date'] = date("d-m-Y", strtotime($job->booking_date));
                    $arr['From'] = $job->from;
                    $arr['To'] = $job->to;
                    $arr['Car Type'] = $job->car_type;
                    $arr['Client Message'] = $job->message;
                    $arr['Driver Remarks'] = $job->remarks;
                    $arr['Payment Message'] = $job->payment_message;
                    $arr['Payment Method'] = $job->type;
                    $arr['Payment Status'] = $job->payment_status;
                    $arr['Total Amount'] = $job->actual_amount;
                    $arr['Cash'] = $job->net_total;
                    $arr['Extra Cost'] = $job->extracharges;
                    
                    $driverdet1 = DB::table('driver')->where('id','=',$job->driver_id)->get();
                     if(count($driverdet1) > 0){
                             $arr['Driver Name'] = $driverdet1[0]->name;
                             $arr['Driver Price'] = $job->driver_amount;
                     }else{
                             $arr['Driver Name'] = '';
                             $arr['Driver Price'] = $job->driver_amount;
                     }
                    
                    $arr['Order status'] = $job->order_status;
                    $arr['Staff Member'] = '';
                    $arr['Booking Date'] = $job->booking_date;
                    $arr['Full Pickup Address'] = $job->pickup_address;
                    $arr['Full Dropoff Address'] = $job->dest_address;
                    
                    
                     $viapoints = DB::table('pick_up_points')->where('booking_id','=',$job->id)->get();
                     if(count($viapoints) > 0){
                         foreach($viapoints as $key => $via){
                        $keys[] = $key;
                    }
                    for($i=0; $i<7; $i++){
                        if(in_array($i, $keys)){
                            $arr['Via '.$i+1] = $viapoints[$i]->location_name;
                        }else{
                            $arr['Via '.$i+1] = '';
                        }
                    }
                     }else{
                         for($i=0; $i<7; $i++){
                            $arr['Via '.$i+1] = '';
                         }
                     }
                     
                     $driverdet = DB::table('driver')->where('id','=',$job->driver_id)->get();
                     if(count($driverdet) > 0){
                            //  $arr['Driver Name'] = $driverdet[0]->name;
                             $arr['Driver Vehicle'] = $driverdet[0]->vech_type;
                             $arr['Vehicle Reg No'] = $driverdet[0]->vech_reg_num;
                             $arr['Vehicle color'] = $driverdet[0]->vech_color;
                             $arr['Vehicle Make'] = $driverdet[0]->make;
                             $arr['Vehicle Model'] = $driverdet[0]->model;
                             $arr['No of Seats'] = $driverdet[0]->no_seat;
                             $arr['Vehicle Insurance'] = $driverdet[0]->vech_insurance;
                             $arr['Insurance Expiry On'] = $driverdet[0]->vech_insur_expiry_date;
                             $arr['Vehicle License'] = $driverdet[0]->vech_licence_no;
                             $arr['License Expiry On'] = $driverdet[0]->vech_lic_expiry_date;
                             $arr['PCO License On'] = $driverdet[0]->pco_licence_no;
                             $arr['PCO Expiry On'] = $driverdet[0]->pco_lic_expiry_date;
                             $arr['Driver License No'] = $driverdet[0]->driver_licence_no;
                             $arr['Driver License Expiry On'] = $driverdet[0]->driver_lic_expiry_date;
                             $arr['MOT No'] = $driverdet[0]->mot_no;
                             $arr['MOT Expiry On'] = $driverdet[0]->mot_expiry_date;
                     }else{
                            //  $arr['Driver Name'] = '';
                             $arr['Driver Vehicle'] = '';
                             $arr['Vehicle Reg No'] = '';
                             $arr['Vehicle color'] = '';
                             $arr['Vehicle Make'] = '';
                             $arr['Vehicle Model'] = '';
                             $arr['No of Seats'] = '';
                             $arr['Vehicle Insurance'] = '';
                             $arr['Insurance Expiry On'] = '';
                             $arr['Vehicle License'] = '';
                             $arr['License Expiry On'] = '';
                             $arr['PCO License On'] = '';
                             $arr['PCO Expiry On'] = '';
                             $arr['Driver License No'] = '';
                             $arr['Driver License Expiry On'] = '';
                             $arr['MOT No'] = '';
                             $arr['MOT Expiry On'] = '';
                     }
                    
                    return $arr;
                    
                    
                    
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

    public function admin_weekly_monthly_export(Request $request)
    {
        $report_type = $request->report_type;

        if ($report_type === 'Monthly') {
            $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
            $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
            $file_name = 'admin_monthly_report_';
        } elseif ($report_type === 'Weekly') {
            $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'admin_weekly_report_';
        } elseif ($report_type === 'Custom') {
            $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
            $file_name = 'admin_custom_report_';
        }

        // $job_details = $job_details = DB::table('bookinfo')
        //     ->select(
        //         'driver.name as Driver_Name',
        //         DB::raw("COUNT(bookinfo.id) as Total_Raids"),
        //         DB::raw("FORMAT(SUM(bookinfo.gross), 2) as Total_Amount"),
        //         DB::raw("FORMAT(SUM(bookinfo.driver_amount), 2) as Driver_Amount"),
        //         DB::raw("FORMAT(SUM(bookinfo.deduct_profit), 2) as Profit_Deduct"),
        //         DB::raw("FORMAT(SUM(bookinfo.commision_profit), 2) as Total_Commission"),
        //         DB::raw("FORMAT(SUM(bookinfo.car_park_amount), 2) as Parking_Charge"),
        //         DB::raw("FORMAT(SUM(bookinfo.driver_final), 2) as Total_Final_Amount")
        //     )
        //     ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
        //     ->where('order_status', '=', 'settled')
        //     ->whereBetween('pickup_date', [$from, $to])
        //     ->groupBy('bookinfo.driver_id')
        //     ->orderBy('total_raids', 'desc')
        //     ->get();
        
        // \DB::enableQueryLog();
        $job_details = DB::table('bookinfo')
            ->select('bookinfo.*',
            'driver.id as d_id',
            'name'
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->where('bookinfo.order_status', '=', 'settled')
            ->whereBetween('bookinfo.pickup_date', [$from, $to])
            // ->groupBy('bookinfo.driver_id')
            ->orderBy('bookinfo.id', 'desc')
            ->get();
// dd(\DB::getQueryLog());
        // $summary_details = DB::table('bookinfo')
        //     ->select(
        //         DB::raw("COUNT('bookinfo.id') as Total_Raids"),
        //         DB::raw("FORMAT(SUM(gross), 2) as Total_Amount"),
        //         DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
        //         DB::raw("FORMAT(SUM(deduct_profit), 2) as Profit_Deduct"),
        //         DB::raw("FORMAT(SUM(commision_profit), 2) as Total_Commission"),
        //         DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
        //         DB::raw("FORMAT(SUM(driver_final), 2) as Total_Final_Amount")
        //     )
        //     ->where('order_status', '=', 'settled')
        //     ->whereBetween('pickup_date', [$from, $to])
        //     ->get();

        if ($job_details->isNotEmpty()) {
            // $sheets = new SheetCollection([
            //     'Job Details' => $job_details,
            //     'Summary' => $summary_details
            // ]);

            // return (new FastExcel($sheets))
            //     ->download($file_name . $from . '_to_' . $to . '.xlsx');
            
            return (new FastExcel($job_details))
                ->download($file_name . $from . '_to_' . $to . '.xlsx', function ($job) {
                    // return [
                    //     'Driver Name' => ucwords(strtolower($job->driver_name)),
                    //     'Job No' => $job->job_no,
                    //     'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
                    //     'From' => $job->from,
                    //     'To' => $job->to,
                    //     'Car Type' => $job->car_type,
                    //     'Total Amount' => $job->total,
                    //     'Order Status' => $job->order_status,
                    // ];
                    
                    $arr['Job No'] = $job->job_no;
                    $clients = DB::table('account')->where('email','=',$job->email)->get();
                    if(count($clients) > 0){
                    foreach($clients as $key => $client){
                        $arr['Client Name'] = $client->f_name;
                        $arr['Client Email'] = $client->email;
                        $arr['Client Mobile'] = $client->phone;
                    }
                    }else{
                        $arr['Client Name'] = $job->fname;
                        $arr['Client Email'] = $job->email;
                        $arr['Client Mobile'] = '';
                    }
                    $arr['Pickup time'] = $job->pickup_time;
                    $arr['Job Date'] = date("d-m-Y", strtotime($job->booking_date));
                    $arr['From'] = $job->from;
                    $arr['To'] = $job->to;
                    $arr['Car Type'] = $job->car_type;
                    $arr['Client Message'] = $job->message;
                    $arr['Driver Remarks'] = $job->remarks;
                    $arr['Payment Message'] = $job->payment_message;
                    $arr['Payment Method'] = $job->type;
                    $arr['Payment Status'] = $job->payment_status;
                    $arr['Total Amount'] = $job->actual_amount;
                    $arr['Cash'] = $job->net_total;
                    $arr['Extra Cost'] = $job->extracharges;
                    
                    $driverdet1 = DB::table('driver')->where('id','=',$job->driver_id)->get();
                     if(count($driverdet1) > 0){
                             $arr['Driver Name'] = $driverdet1[0]->name;
                             $arr['Driver Price'] = $job->driver_amount;
                     }else{
                             $arr['Driver Name'] = '';
                             $arr['Driver Price'] = $job->driver_amount;
                     }
                    
                    $arr['Order status'] = $job->order_status;
                    $arr['Staff Member'] = '';
                    $arr['Booking Date'] = $job->booking_date;
                    $arr['Full Pickup Address'] = $job->pickup_address;
                    $arr['Full Dropoff Address'] = $job->dest_address;
                    
                    
                     $viapoints = DB::table('pick_up_points')->where('booking_id','=',$job->id)->get();
                     if(count($viapoints) > 0){
                         foreach($viapoints as $key => $via){
                        $keys[] = $key;
                    }
                    for($i=0; $i<7; $i++){
                        if(in_array($i, $keys)){
                            $arr['Via '.$i+1] = $viapoints[$i]->location_name;
                        }else{
                            $arr['Via '.$i+1] = '';
                        }
                    }
                     }else{
                         for($i=0; $i<7; $i++){
                            $arr['Via '.$i+1] = '';
                         }
                     }
                     
                     $driverdet = DB::table('driver')->where('id','=',$job->driver_id)->get();
                     if(count($driverdet) > 0){
                            //  $arr['Driver Name'] = $driverdet[0]->name;
                            //  $arr['Driver Price'] = $driverdet[0]->actual;
                             $arr['Driver Vehicle'] = $driverdet[0]->vech_type;
                             $arr['Vehicle Reg No'] = $driverdet[0]->vech_reg_num;
                             $arr['Vehicle color'] = $driverdet[0]->vech_color;
                             $arr['Vehicle Make'] = $driverdet[0]->make;
                             $arr['Vehicle Model'] = $driverdet[0]->model;
                             $arr['No of Seats'] = $driverdet[0]->no_seat;
                             $arr['Vehicle Insurance'] = $driverdet[0]->vech_insurance;
                             $arr['Insurance Expiry On'] = $driverdet[0]->vech_insur_expiry_date;
                             $arr['Vehicle License'] = $driverdet[0]->vech_licence_no;
                             $arr['License Expiry On'] = $driverdet[0]->vech_lic_expiry_date;
                             $arr['PCO License On'] = $driverdet[0]->pco_licence_no;
                             $arr['PCO Expiry On'] = $driverdet[0]->pco_lic_expiry_date;
                             $arr['Driver License No'] = $driverdet[0]->driver_licence_no;
                             $arr['Driver License Expiry On'] = $driverdet[0]->driver_lic_expiry_date;
                             $arr['MOT No'] = $driverdet[0]->mot_no;
                             $arr['MOT Expiry On'] = $driverdet[0]->mot_expiry_date;
                     }else{
                            //  $arr['Driver Name'] = '';
                            //  $arr['Driver Price'] = '';
                             $arr['Driver Vehicle'] = '';
                             $arr['Vehicle Reg No'] = '';
                             $arr['Vehicle color'] = '';
                             $arr['Vehicle Make'] = '';
                             $arr['Vehicle Model'] = '';
                             $arr['No of Seats'] = '';
                             $arr['Vehicle Insurance'] = '';
                             $arr['Insurance Expiry On'] = '';
                             $arr['Vehicle License'] = '';
                             $arr['License Expiry On'] = '';
                             $arr['PCO License On'] = '';
                             $arr['PCO Expiry On'] = '';
                             $arr['Driver License No'] = '';
                             $arr['Driver License Expiry On'] = '';
                             $arr['MOT No'] = '';
                             $arr['MOT Expiry On'] = '';
                     }
                    
                    return $arr;
                    
                    
                    
                });
            
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

    public function driver_daily_export(Request $request)
    {
        $driver_id = $request->driver_id;
        $date = Carbon::parse($request->date_filter)->format('Y-m-d');
        $file_name = 'driver_day_report_';
// \DB::enableQueryLog();
        $job_details = DB::table('bookinfo')
            ->select('bookinfo.*',
            'driver.id as d_id',
            'name'
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->where('pickup_date', '=', $date)
            ->where('driver_id', '=', $driver_id)
            ->get();
            // dd(\DB::getQueryLog());
            
            
        if ($job_details->isNotEmpty()) {
            return (new FastExcel($job_details))
                ->download($file_name . driver_name_for_file($driver_id) . '_' . $date . '.xlsx', function ($job) {
                    
                    $arr1 = [
                        'Job No' => $job->job_no,
                        'Clients Name' => $job->fname,
                        'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
                        'From' => $job->from,
                        'To' => $job->to,
                        'Car Type' => $job->car_type,
                        'Client Message' => $job->message,
                        'Driver Remarks' => $job->remarks,
                        'Payment Message' => $job->payment_message,
                        'Payment Method' => $job->type,
                        'Payment Status' => $job->payment_status,
                        'Total Amount' => $job->actual_amount,
                        'Cash' => $job->net_total,
                        'Extra cost' => $job->extracharges,
                        'Driver Price' => $job->driver_amount,
                        'Driver Name' => $job->name,
                        'Order Status' => $job->order_status,
                        'Staff Member' => '',
                        'Booking Date' => $job->booking_date
                    ];
                    
                     $viapoints = DB::table('pick_up_points')->where('booking_id','=',$job->id)->get();
                     if(count($viapoints) > 0){
                    foreach($viapoints as $key => $via){
                        $keys[] = $key;
                    }
                    for($i=0; $i<7; $i++){
                        if(in_array($i, $keys)){
                            $arr1['Via '.$i+1] = $viapoints[$i]->location_name;
                        }else{
                            $arr1['Via '.$i+1] = '';
                        }
                    }
                     }else{
                          for($i=0; $i<7; $i++){
                            $arr1['Via '.$i+1] = '';
                        }
                     }
                    
                    $arr1['Full pickup address'] = $job->pickup_address;
                    $arr1['Full dropoff address'] = $job->dest_address;
                    
                    return $arr1;
                    
                    
                    
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }

    public function driver_weekly_monthly_export(Request $request)
    {
        $driver_id = $request->driver_id;
        $report_type = $request->report_type; //Monthly

        if ($report_type === 'Monthly' || $report_type === 'Custom') {
            if($report_type === 'Monthly'){
                $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
                $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
                $file_name = 'driver_monthly_report_';
            } elseif($report_type === 'Custom'){
                $data['from'] = $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
                $data['to'] = $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
                $file_name = 'driver_custom_report_';
            }

            $settled_job = DB::table('transaction')
                ->select('*')
                ->where('driver_id', '=', $driver_id)
                ->whereBetween('fromdate', [$from, $to])
                ->whereBetween('todate', [$from, $to])
                ->get()
                ->toArray();

            if (empty($settled_job)) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }

            $all_jobs_id_string = $this->helper_service->getJobID($settled_job);

            $all_trans_id_string = $this->helper_service->getTransID($settled_job);

            $all_jobs_id_array = explode(',', $all_jobs_id_string);

            $all_trans_id_array = explode(',', $all_trans_id_string);

            $last_trans_id = end($all_trans_id_array);

            $settle_history = DB::table('settle_history')
                ->select(
                    DB::raw("FORMAT(old_balance, 2) as old_balance"),
                    DB::raw("FORMAT(current_balance, 2) as current_balance"),
                )
                ->where('trans_id', $last_trans_id)
                ->get()
                ->first();

            $summary_details = DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT('bookinfo.id') as Total_Raids"),
                    DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                    DB::raw("FORMAT(SUM(commision_profit), 2) as Commission_Amount"),
                    DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                    DB::raw("FORMAT(SUM(driver_final), 2) as Final_Amount")
                )
                ->whereIn('id', $all_jobs_id_array)
                ->get();
                
                $job_details = DB::table('bookinfo')
                ->select('bookinfo.*',
            'driver.id as d_id',
            'name'
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->whereIn('bookinfo.id', $all_jobs_id_array)
            ->orderBy('bookinfo.pickup_date', 'ASC')
            ->orderBy('bookinfo.pickup_time', 'ASC')
            ->get(); 

            // $job_details = DB::table('bookinfo')
            //     ->select(
            //         DB::raw("job_no as Job_No"),
            //         DB::raw("pickup_date as Pickup_Date"),
            //         DB::raw("pickup_time as Pickup_Time"),
            //         DB::raw("`from` as `From`"),
            //         DB::raw("`to` as `To`"),
            //         DB::raw("car_type as Car_Type"),
            //         DB::raw("gross as Gross"),
            //         DB::raw("driver_amount as Driver_Amount"),
            //         DB::raw("deduct_profit as Deduct_Profit"),
            //         DB::raw("car_park_amount as Parking_Charge"),
            //         DB::raw("commision_profit as Commission"),
            //         DB::raw("driver_final as Driver_Final")
            //     )
            //     ->whereIn('id', $all_jobs_id_array)
            //     ->orderBy('pickup_date', 'ASC')
            //     ->orderBy('pickup_time', 'ASC')
            //     ->get();
        } elseif ($report_type === 'Weekly') {
            $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'driver_weekly_report_';
            
            
// \DB::enableQueryLog();
            $settled_job = DB::table('transaction')
                ->select('id', 'jobid')
                ->where('driver_id', '=', $driver_id)
                ->where('fromdate', '=', $from)
                ->where('todate', '=', $to)
                ->get()
                ->first();
// dd(\DB::getQueryLog());

            if (!$settled_job) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }

            $settle_history = DB::table('settle_history')
                ->select(
                    DB::raw("FORMAT(old_balance, 2) as old_balance"),
                    DB::raw("FORMAT(current_balance, 2) as current_balance"),
                )
                ->where('trans_id', '=', $settled_job->id)
                ->get()
                ->first();

            $summary_details = DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT('bookinfo.id') as Total_Raids"),
                    DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                    DB::raw("FORMAT(SUM(commision_profit), 2) as Commission_Amount"),
                    DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                    DB::raw("FORMAT(SUM(driver_final), 2) as Final_Amount")
                )
                ->whereIn('id', explode(',', $settled_job->jobid))
                ->get();
// \DB::enableQueryLog();
            $job_details = DB::table('bookinfo')
                ->select('bookinfo.*',
            'driver.id as d_id',
            'name'
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->whereIn('bookinfo.id', explode(',', $settled_job->jobid))
            ->orderBy('bookinfo.pickup_date', 'ASC')
            ->orderBy('bookinfo.pickup_time', 'ASC')
            ->get(); 
                // dd(\DB::getQueryLog());
        }

        if ($job_details->isNotEmpty()) {
            $summary_details->map(function ($detail) use ($settle_history) {
                $detail->Old_Balance = $settle_history->old_balance;
                $detail->Current_Balance = $settle_history->current_balance;
                return $detail;
            });

            // $sheets = new SheetCollection([
            //     'Job Details' => $job_details,
            //     'Summary' => $summary_details
            // ]);

            // return (new FastExcel($sheets))
            //     ->download($file_name . driver_name_for_file($driver_id) . '_' . $from . '_to_' . $to . '.xlsx');
            
            if ($job_details->isNotEmpty()) {
            return (new FastExcel($job_details))
                ->download($file_name . driver_name_for_file($driver_id) . '_' . $from . '_to_' . $to . '.xlsx', function ($job) {
                    
                    $arr1 = [
                        'Job No' => $job->job_no,
                        'Clients Name' => $job->fname,
                        'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
                        'From' => $job->from,
                        'To' => $job->to,
                        'Car Type' => $job->car_type,
                        'Client Message' => $job->message,
                        'Driver Remarks' => $job->remarks,
                        'Payment Message' => $job->payment_message,
                        'Payment Method' => $job->type,
                        'Payment Status' => $job->payment_status,
                        'Total Amount' => $job->actual_amount,
                        'Cash' => $job->net_total,
                        'Extra cost' => $job->extracharges,
                        'Driver Price' => $job->driver_amount,
                        'Driver Name' => $job->name,
                        'Order Status' => $job->order_status,
                        'Staff Member' => '',
                        'Booking Date' => $job->booking_date
                    ];
                    
                    $viapoints = DB::table('pick_up_points')->where('booking_id','=',$job->id)->get();
             if(count($viapoints) > 0){
                //  dd(count($viapoints));
                    foreach($viapoints as $key => $via){
                        $keys[] = $key;
                    }
                    // dd($keys);
                    for($i=0; $i<7; $i++){
                        if(in_array($i, $keys)){
                            $arr1['Via '.$i+1] = $viapoints[$i]->location_name;
                        }else{
                            $arr1['Via '.$i+1] = '';
                        }
                    }
             }else{
                  for($i=0; $i<7; $i++){
                            $arr1['Via '.$i+1] = '';
                        }
             }
                    
                    $arr1['Full pickup address'] = $job->pickup_address;
                    $arr1['Full dropoff address'] = $job->dest_address;
                    
                    return $arr1;
                    
                });
        }
            
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }
}
