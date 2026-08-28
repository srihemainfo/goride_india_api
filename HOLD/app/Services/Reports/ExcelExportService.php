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
    $device_id = $request->device_id;
    $token = $request->token; 
    $job_type=$request->job_type;
    $file_name = 'admin_day_report_' . str_replace([' ', ':'], '_', $date); // Use underscores instead of spaces
    $apiUrl = '{{env('API_URL')}}admin-generate-report-excel';
    $response = $this->GetAPIRequest($apiUrl, $date, $device_id, $token,$job_type);

    if ($response === false) {
        return 'Error making the request';
       
    }

    $job_details = $response;

    if (empty($job_details)) {
        return view('reports.no_data_message', ['report_type' => 'admin']);
    }

    $excelData = [];
    foreach ($job_details as $job) {
        
        $arr = [
            'Driver Name' => ucwords(strtolower($job->name)),
            'Job No' => $job->job_no,
            'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
            'From' => $job->from,
            'To' => $job->to,
            'Car Type' => $job->car_type,
            'Total Amount' => $job->total,
            'Order Status' => $job->order_status,
            // Add other common fields here
        ];


                    $arr['Job No'] = $job->job_no;

                    $date=$job->email;
                    //account details
                            $apiUrl = '{{env('API_URL')}}AdminGenerateReportExcelaccount';
                            $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
                            if ($response === false) {
                                echo 'Error making the request';
                            } else {
                                $clients = $response;
                            }
                            
                    
                    foreach($clients as  $client){
                        $arr['Client Name'] = $client->f_name;
                        $arr['Client Email'] = $client->email;
                        $arr['Client Mobile'] = $client->phone;
                    }
                    $arr['Pickup Date'] = $job->pickup_date;
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

                            $date=$job->driver_id;
                            $apiUrl = '{{env('API_URL')}}AdminGenerateReportExceldriver';
                            $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
                            if ($response === false) {
                                echo 'Error making the request';
                            } else {
                                $driverdet1 = $response;
                            }
                    
                            
                    
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

                    //driver excel
                      $date=$job->driver_id;
                            $apiUrl = '{{env('API_URL')}}AdminGenerateReportExceldriver';
                            $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
                            if ($response === false) {
                                echo 'Error making the request';
                            } else {
                                $driverdet = $response;
                            }
                            
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

        $excelData[] = $arr;
    }

    // Use FastExcel to download the data as an Excel file
    return (new FastExcel($excelData))->download($file_name . '.xlsx');
}
    


    // public function admin_weekly_monthly_export(Request $request)
    // {
    //     // dd($request->all());
        
    //     $report_type = $request->report_type;
    //     $job_type = $request->job_type;
        
    //     if ($report_type === 'Monthly') {
    //         $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
    //         $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
    //         $file_name = 'admin_monthly_report_';
    //     } elseif ($report_type === 'Weekly') {
    //         $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
    //         $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
    //         $file_name = 'admin_weekly_report_';
    //     } elseif ($report_type === 'Custom') {
    //         $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
    //         $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
    //         $file_name = 'admin_custom_report_';
    //     }
    
    //     $device_id = $request->device_id;
    //     $token = $request->token;
    //     $apiUrl = '{{env('API_URL')}}AdminGenerateReportExcelwekly_monthly';
    //     $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token,$job_type);
    //     if ($response === false) {
    //         echo 'Error making the request';
    //     } else {
    //         $job_details = $response;
    //     }
    
    //   if (empty($job_details)) {
    //         return view('reports.no_data_message', ['report_type' => 'admin']);
    //     }
    
    //     $excelData = [];
    //     foreach ($job_details as $job) {
    //         $arr = [
    //             'Driver Name' => ucwords(strtolower($job->name)),
    //             'Job No' => $job->job_no,
    //             'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
    //             'From' => $job->from,
    //             'To' => $job->to,
    //             'Car Type' => $job->car_type,
    //             'Total Amount' => $job->total,
    //             'Order Status' => $job->order_status,
    //             // Add other common fields here
    //         ];
    
    //         // Additional fields for each row
    //                             $arr['Job No'] = $job->job_no;
    //                     // $clients = DB::table('account')->where('email','=',$job->email)->get();
    //                     $date=$job->email;
    //                     //account details
    //                             $apiUrl = '{{env('API_URL')}}AdminGenerateReportExcelaccount';
    //                             $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
    //                             if ($response === false) {
    //                                 echo 'Error making the request';
    //                             } else {
    //                                 $clients = $response;
    //                             }
                        
    //                     foreach($clients as  $client){
    //                         $arr['Client Name'] = $client->f_name;
    //                         $arr['Client Email'] = $client->email;
    //                         $arr['Client Mobile'] = $client->phone;
    //                     }
    //                     $arr['Pickup Date'] = $job->pickup_date;
    //                     $arr['Pickup time'] = $job->pickup_time;
    //                     $arr['Job Date'] = date("d-m-Y", strtotime($job->booking_date));
    //                     $arr['From'] = $job->from;
    //                     $arr['To'] = $job->to;
    //                     $arr['Car Type'] = $job->car_type;
    //                     $arr['Client Message'] = $job->message;
    //                     $arr['Driver Remarks'] = $job->remarks;
    //                     $arr['Payment Message'] = $job->payment_message;
    //                     $arr['Payment Method'] = $job->type;
    //                     $arr['Payment Status'] = $job->payment_status;
    //                     $arr['Total Amount'] = $job->actual_amount;
    //                     $arr['Cash'] = $job->net_total;
    //                     $arr['Extra Cost'] = $job->extracharges;
                        
    //                     // $driverdet1 = DB::table('driver')->where('id','=',$job->driver_id)->get();
                        
    //                     //driver excel
    //                             $date=$job->driver_id;
    //                             $apiUrl = '{{env('API_URL')}}AdminGenerateReportExceldriver';
    //                             $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
    //                             if ($response === false) {
    //                                 echo 'Error making the request';
    //                             } else {
    //                                 $driverdet1 = $response;
    //                             }
                        
                        
    //                      if(count($driverdet1) > 0){
    //                              $arr['Driver Name'] = $driverdet1[0]->name;
    //                             //  $arr['Driver Price'] = $job->driver_amount;
    //                             $arr['Driver Price'] = !empty($job->driver_amount) ? $job->driver_amount : 0;
    //                      }else{
    //                              $arr['Driver Name'] = '';
    //                             //  $arr['Driver Price'] = $job->driver_amount;
    //                             $arr['Driver Price'] = !empty($job->driver_amount) ? $job->driver_amount : 0;
    //                      }
                        
    //                     $arr['Order status'] = $job->order_status;
    //                     $arr['Staff Member'] = '';
    //                     $arr['Booking Date'] = $job->booking_date;
    //                     $arr['Full Pickup Address'] = $job->pickup_address;
    //                     $arr['Full Dropoff Address'] = $job->dest_address;

                        
    //                     //driver excel
    //                       $date=$job->driver_id;
    //                             $apiUrl = '{{env('API_URL')}}AdminGenerateReportExceldriver';
    //                             $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
    //                             if ($response === false) {
    //                                 echo 'Error making the request';
    //                             } else {
    //                                 $driverdet = $response;
    //                             }
    //                             //dd($driverdet);
                                
    //                      if(($driverdet) > 0){
    //                 $arr['Driver Vehicle'] = !empty($driverdet[0]->vech_type) ? $driverdet[0]->vech_type : '';
    //                 $arr['Vehicle Reg No'] = !empty($driverdet[0]->vech_reg_num) ? $driverdet[0]->vech_reg_num : '';
    //                 $arr['Vehicle color'] = !empty($driverdet[0]->vech_color) ? $driverdet[0]->vech_color : '';
    //                 $arr['Vehicle Make'] = !empty($driverdet[0]->make) ? $driverdet[0]->make : '';
    //                 $arr['Vehicle Model'] = !empty($driverdet[0]->model) ? $driverdet[0]->model : '';
    //                 $arr['No of Seats'] = !empty($driverdet[0]->no_seat) ? $driverdet[0]->no_seat : '';
    //                 $arr['Vehicle Insurance'] = !empty($driverdet[0]->vech_insurance) ? $driverdet[0]->vech_insurance : '';
    //                 $arr['Insurance Expiry On'] = !empty($driverdet[0]->vech_insur_expiry_date) ? $driverdet[0]->vech_insur_expiry_date : '';
    //                 $arr['Vehicle License'] = !empty($driverdet[0]->vech_licence_no) ? $driverdet[0]->vech_licence_no : '';
    //                 $arr['License Expiry On'] = !empty($driverdet[0]->vech_lic_expiry_date) ? $driverdet[0]->vech_lic_expiry_date : '';
    //                 $arr['PCO License On'] = !empty($driverdet[0]->pco_licence_no) ? $driverdet[0]->pco_licence_no : '';
    //                 $arr['PCO Expiry On'] = !empty($driverdet[0]->pco_lic_expiry_date) ? $driverdet[0]->pco_lic_expiry_date : '';
    //                 $arr['Driver License No'] = !empty($driverdet[0]->driver_licence_no) ? $driverdet[0]->driver_licence_no : '';
    //                 $arr['Driver License Expiry On'] = !empty($driverdet[0]->driver_lic_expiry_date) ? $driverdet[0]->driver_lic_expiry_date : '';
    //                 $arr['MOT No'] = !empty($driverdet[0]->mot_no) ? $driverdet[0]->mot_no : '';
    //                 $arr['MOT Expiry On'] = !empty($driverdet[0]->mot_expiry_date) ? $driverdet[0]->mot_expiry_date : '';

    //                      }else{
    //                             //  $arr['Driver Name'] = '';
    //                              $arr['Driver Vehicle'] = '';
    //                              $arr['Vehicle Reg No'] = '';
    //                              $arr['Vehicle color'] = '';
    //                              $arr['Vehicle Make'] = '';
    //                              $arr['Vehicle Model'] = '';
    //                              $arr['No of Seats'] = '';
    //                              $arr['Vehicle Insurance'] = '';
    //                              $arr['Insurance Expiry On'] = '';
    //                              $arr['Vehicle License'] = '';
    //                              $arr['License Expiry On'] = '';
    //                              $arr['PCO License On'] = '';
    //                              $arr['PCO Expiry On'] = '';
    //                              $arr['Driver License No'] = '';
    //                              $arr['Driver License Expiry On'] = '';
    //                              $arr['MOT No'] = '';
    //                              $arr['MOT Expiry On'] = '';
    //                      }
    
    //         $excelData[] = $arr;
            
    //     }
    
    // dd($excelData);
    //     return (new FastExcel($excelData))->download($file_name . '.xlsx');
    // }
    
    public function admin_weekly_monthly_export(Request $request)
{
    $report_type = $request->report_type;
    $job_type = $request->job_type;
    
    if ($report_type === 'Monthly') {
        $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
        $to = (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
        $file_name = 'admin_monthly_report_';
    } elseif ($report_type === 'Weekly') {
        $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
        $to = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
        $file_name = 'admin_weekly_report_';
    } elseif ($report_type === 'Custom') {
        $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
        $file_name = 'admin_custom_report_';
    }

    $device_id = $request->device_id;
    $token = $request->token;
    $apiUrl = '{{env('API_URL')}}AdminGenerateReportExcelwekly_monthly';
    $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token, $job_type);

    if ($response === false) {
        return view('reports.error_message', ['message' => 'Error making the request']);
    }

    $job_details = $response;

    if (empty($job_details)) {
        return view('reports.no_data_message', ['report_type' => 'admin']);
    }

    $excelData = [];

    foreach ($job_details as $job) {
        // Common job details
        $arr = [
            'Driver Name' => ucwords(strtolower($job->name)),
            'Job No' => $job->job_no,
            'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
            'From' => $job->from,
            'To' => $job->to,
            'Car Type' => $job->car_type,
            'Total Amount' => $job->total,
            'Order Status' => $job->order_status,
            'Job Date' => date("d-m-Y", strtotime($job->booking_date)),
            'Pickup Date' => $job->pickup_date,
            'Pickup time' => $job->pickup_time,
            'Client Message' => $job->message,
            'Driver Remarks' => $job->remarks,
            'Payment Message' => $job->payment_message,
            'Payment Method' => $job->type,
            'Payment Status' => $job->payment_status,
            'Total Amount' => $job->actual_amount,
            'Cash' => $job->net_total,
            'Extra Cost' => $job->extracharges,
            'Full Pickup Address' => $job->pickup_address,
            'Full Dropoff Address' => $job->dest_address,
        ];

        // Fetch client details
        $apiUrlClient = '{{env('API_URL')}}AdminGenerateReportExcelaccount';
        $responseClient = $this->GetAPIRequest($apiUrlClient, $job->email, $device_id, $token, $job_type);

        if ($responseClient !== false && count($responseClient) > 0) {
            $client = $responseClient[0];
            $arr['Client Name'] = $client->f_name;
            $arr['Client Email'] = $client->email;
            $arr['Client Mobile'] = $client->phone;
        } else {
            $arr['Client Name'] = '';
            $arr['Client Email'] = '';
            $arr['Client Mobile'] = '';
        }

        // Fetch driver details
        $apiUrlDriver = '{{env('API_URL')}}AdminGenerateReportExceldriver';
        $responseDriver = $this->GetAPIRequest($apiUrlDriver, $job->driver_id, $device_id, $token, $job_type);

        if ($responseDriver !== false && count($responseDriver) > 0) {
            $driver = $responseDriver[0];
            $arr['Driver Vehicle'] = !empty($driver->vech_type) ? $driver->vech_type : '';
            $arr['Vehicle Reg No'] = !empty($driver->vech_reg_num) ? $driver->vech_reg_num : '';
            $arr['Vehicle color'] = !empty($driver->vech_color) ? $driver->vech_color : '';
            $arr['Vehicle Make'] = !empty($driver->make) ? $driver->make : '';
            $arr['Vehicle Model'] = !empty($driver->model) ? $driver->model : '';
            $arr['No of Seats'] = !empty($driver->no_seat) ? $driver->no_seat : '';
            $arr['Vehicle Insurance'] = !empty($driver->vech_insurance) ? $driver->vech_insurance : '';
            $arr['Insurance Expiry On'] = !empty($driver->vech_insur_expiry_date) ? $driver->vech_insur_expiry_date : '';
            $arr['Vehicle License'] = !empty($driver->vech_licence_no) ? $driver->vech_licence_no : '';
            $arr['License Expiry On'] = !empty($driver->vech_lic_expiry_date) ? $driver->vech_lic_expiry_date : '';
            $arr['PCO License On'] = !empty($driver->pco_licence_no) ? $driver->pco_licence_no : '';
            $arr['PCO Expiry On'] = !empty($driver->pco_lic_expiry_date) ? $driver->pco_lic_expiry_date : '';
            $arr['Driver License No'] = !empty($driver->driver_licence_no) ? $driver->driver_licence_no : '';
            $arr['Driver License Expiry On'] = !empty($driver->driver_lic_expiry_date) ? $driver->driver_lic_expiry_date : '';
            $arr['MOT No'] = !empty($driver->mot_no) ? $driver->mot_no : '';
            $arr['MOT Expiry On'] = !empty($driver->mot_expiry_date) ? $driver->mot_expiry_date : '';
            $arr['Driver Price'] = !empty($job->driver_amount) ? $job->driver_amount : 0;
        } else {
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
            $arr['Driver Price'] = !empty($job->driver_amount) ? $job->driver_amount : 0;
        }

        $excelData[] = $arr;
    }

    // Generate Excel file
    return (new FastExcel($excelData))->download($file_name . '.xlsx');
}



    public function GetAPIRequest($url,$date,$device_id,$token,$job_type){
        $api_url = $url;
        $csrfToken = $token;
        $requestData = [
            "date_filter" => $date,
            "device_id" => $device_id,
            "token" => $csrfToken, 
            "job_type" => $job_type 
        ];
        $jsonRequestData = json_encode($requestData);
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonRequestData),
            'X-CSRF-TOKEN: ' . $csrfToken 
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestData);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        return $result;
    }


    public function driver_daily_export(Request $request)
    {
        
        $driver_id = $request->driver_id;
        $date = Carbon::parse($request->date_filter)->format('Y-m-d');
        $file_name = 'driver_day_report_';
        $job_type=$driver_id;
        $device_id=$request->device_id;
        $token=$request->d_token;
        $from=$date;
        $to="daily";
    //api data get  transaction table
            $apiUrl = '{{env('API_URL')}}driverreportexceltransactiondaily';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $job_details = $response;
            }
            
        // if ($job_details->isNotEmpty()) {
        if (!empty($job_details)) {
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
                    
                    //  $viapoints = DB::table('pick_up_points')->where('booking_id','=',$job->id)->get();
                    //  if(count($viapoints) > 0){
                    // foreach($viapoints as $key => $via){
                    //     $keys[] = $key;
                    // }
                    // for($i=0; $i<7; $i++){
                    //     if(in_array($i, $keys)){
                    //         $arr1['Via '.$i+1] = $viapoints[$i]->location_name;
                    //     }else{
                    //         $arr1['Via '.$i+1] = '';
                    //     }
                    // }
                    //  }else{
                    //       for($i=0; $i<7; $i++){
                    //         $arr1['Via '.$i+1] = '';
                    //     }
                    //  }
                    
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
        $job_type=$driver_id;
        $device_id=$request->device_id;
        $token=$request->d_token;
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
            //api data get  transaction table
            $apiUrl = '{{env('API_URL')}}driverreportexceltransaction';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $settled_job = $response;
            }
            if (empty($settled_job)) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }

            $all_jobs_id_string = $this->helper_service->getJobID($settled_job);

            $all_trans_id_string = $this->helper_service->getTransID($settled_job);

            $all_jobs_id_array = explode(',', $all_jobs_id_string);

            $all_trans_id_array = explode(',', $all_trans_id_string);

            $last_trans_id = end($all_trans_id_array);

            // get api data settle_history
            $job_type=$last_trans_id;
            $apiUrl = '{{env('API_URL')}}driverreportexcelsettle_history';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $settle_history = $response;
            }
                                 
            //get data api bookinfo
            
            $job_type=$all_jobs_id_array;
            $apiUrl = '{{env('API_URL')}}driverreportexcelsummary_details';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $summary_details = $response;
            }
            //get api data in bookinginfo 
            
            $job_type=$all_jobs_id_array;
            $apiUrl = '{{env('API_URL')}}driverreportexcelbookinfo';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $job_details = $response;
            }
        } elseif ($report_type === 'Weekly') {
            $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'driver_weekly_report_';
            // get api data transaction
            $apiUrl = '{{env('API_URL')}}driverreportexceltransaction';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $settled_job = $response;
            }
            if (!$settled_job) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }
            
            $job_ids = [];
            foreach ($settled_job as $job) {
                $job_ids[] = $job->id;
            }
            
            // get data api  settle_history table
            $job_type=$job_ids;
            $job_type=implode(',',$job_type);
            $apiUrl = '{{env('API_URL')}}driverreportexcelsettle_history';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $settle_history = $response;
            }
            // get data api in bookinfo table
            $job_type=$job_type;
            $apiUrl = '{{env('API_URL')}}driverreportexcelsummary_detailsexcel';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $summary_details = $response;
            }
           // get data api bokinfo table     
                
            $job_type=$job_type;
            $apiUrl = '{{env('API_URL')}}driverreportexcelbookinfoexcel';
            $response = $this->getApiRequestweekly_monthly($apiUrl, $from, $to, $job_type, $device_id, $token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $job_details = $response;
            }    
        }

        if (!empty($job_details)) {
        $summary_details = collect($summary_details);
        $summary_details->map(function ($detail) use ($settle_history) {
            $detail->Old_Balance = $settle_history->old_balance;
            $detail->Current_Balance = $settle_history->current_balance;
            return $detail;
        });
            if (!empty($job_details)) {
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
                        'Driver Name' => $job->fname,
                        'Order Status' => $job->order_status,
                        'Staff Member' => '',
                        'Booking Date' => $job->booking_date
                    ];
                    
                    $arr1['Full pickup address'] = $job->pickup_address;
                    $arr1['Full dropoff address'] = $job->dest_address;
                    
                    return $arr1;
                    
                });
        }
            
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }
    
    public function GetAPIRequestweekly_monthly($url,$from,$to,$job_type,$device_id,$token){
        $api_url = $url;
        $csrfToken = $token;
        $requestData = [
            "from" => $from,
            "to" => $to,
            "device_id" => $device_id,
            "token" => $csrfToken, 
            "job_type" => $job_type 
        ];
        $jsonRequestData = json_encode($requestData);
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonRequestData),
            'X-CSRF-TOKEN: ' . $csrfToken 
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestData);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        return $result;
    }   
    
}
