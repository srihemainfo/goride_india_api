<?php

namespace App\Http\Controllers;

use App\Services\Reports\{ExcelExportService, ReportsHelperService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use Barryvdh\DomPDF\Facade\Pdf as DOMPDF;
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel};
use App\Services\Permissions\PermissionHelperService;

class ReportsController extends Controller
{
    private $excel_export;
    private $helper_service;
    private $permission;

    public function __construct()
    {
        $this->excel_export = new ExcelExportService;
        $this->helper_service = new ReportsHelperService;
        $this->permission = new PermissionHelperService;
    }

    public function ManageAdminReport(Request $request)
    {
        $this->permission->check_privilege('ADMIN_REPORT_MODULE', self::ACTION_TYPE['index']);
        $month_array = $this->helper_service->getMonthArray();
        $week_array = $this->helper_service->getWeekArray();
        return view('reports.manage_admin_report', compact('month_array', 'week_array'));
    }

    public function ManagejobReport(Request $request)
    {
        
        return view('reports.manage_job_report');
    }

    public function ManageDriverReport(Request $request)
    {
        $cookieHeader = $request->header('Cookie');
       if ($cookieHeader) {
            $cookies = explode('; ', $cookieHeader);
    
            foreach ($cookies as $cookie) {
                list($name, $value) = explode('=', $cookie, 2);
    
                if ($name === 'd_token') {
                    $token = $value;
                    break; 
                }
            }
        } else {
            echo "Cookie token not found.";
        }
        $device_id=0;
        $this->permission->check_privilege('DRIVER_REPORT_MODULE', self::ACTION_TYPE['index']);
        $month_array = $this->helper_service->getMonthArray();
        $week_array = $this->helper_service->getWeekArray();
        
        $apiUrl = env('API_URL').'driverlistall';
        $response = $this->GetAPIRequestdriver($apiUrl,$device_id,$token);
        // dd($response);
        if ($response === false) {
            echo 'Error making the request';
        } else {
            $list_drivers =$response;
        }
        

        return view('reports.manage_driver_report', compact('list_drivers', 'month_array', 'week_array'));
    }

    public function AdminGenerateReport(Request $request)
    {
        
        // dd($request->all());
        
        $this->permission->check_privilege('ADMIN_REPORT_MODULE', self::ACTION_TYPE['index']);
        $data = [];
        $data['report_type'] = $report_type = $request->report_type; 
        $data['device_id'] = $device_id = $request->device_id; 
        $data['token'] = $token = $request->token; 
        $data['job_type'] = $job_type = $request->job_type; 
        
        if ($request->excel == 1 && $report_type === 'Daily') {
            return $this->excel_export->admin_daily_export($request);
        } elseif ($request->excel == 1 && ($report_type === 'Monthly' || $report_type === 'Weekly' || $report_type === 'Custom')) {
            return $this->excel_export->admin_weekly_monthly_export($request);
        }

        if ($report_type === 'Monthly') {
            $data['from'] = $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
            $data['to'] = $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
            $file_name = 'admin_monthly_report_';
        } elseif ($report_type === 'Weekly') {
            $data['from'] = $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $data['to'] = $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'admin_weekly_report_';
        } elseif ($report_type === 'Custom') {
            [$fromDate, $toDate] = explode(' - ', $request->custom_filter);

            $data['from'] = $from = Carbon::createFromFormat('m/d/Y', trim($fromDate))->format('Y-m-d');
            $data['to'] = $to = Carbon::createFromFormat('m/d/Y', trim($toDate))->format('Y-m-d');
            
            $file_name = 'admin_custom_report_';
        } elseif ($report_type === 'Daily') {
            $data['date'] = $date = Carbon::parse($request->date_filter)->format('Y-m-d');
            $file_name = 'admin_day_report_';
            $apiUrl = env('API_URL').'admin-generate-report';
            $response = $this->GetAPIRequest($apiUrl,$date,$device_id,$token,$job_type);
            // dd($response);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $pastdraw_result =$response;
            }
 
            $data['job_details'] = $job_details = $pastdraw_result;
           
            $data['summary_details'] = $pastdraw_result;
            // dd($job_details);

            // if ($job_details->isNotEmpty()) {
            if (!empty($job_details->job_details)) {
                $pdf = DOMPDF::loadView('reports.admin_day_report_pdf', $data)->setPaper('a4', 'portrait');
                return $pdf->stream($file_name . $date . '.pdf');
            } else {
                return view('reports.no_data_message', ['report_type' => 'admin']);
            }
        }

        $file_name = 'admin_report_';
        $apiUrl = env('API_URL').'admin-generate-report-weekly';
        $response = $this->GetAPIRequestweekly($apiUrl,$from,$to,$device_id,$token,$job_type);
        // dd($response);
        if ($response === false) {
            echo 'Error making the request';
        } else {
            $pastdraw_result =$response;
        }

        //dd($pastdraw_result);

        $data['job_details'] = $job_details = $pastdraw_result;
       
        $data['summary_details'] = $pastdraw_result;
        
//dd($data);

        if (!empty($job_details->job_details)) {
            $pdf = DOMPDF::loadView('reports.admin_report_pdf', $data)->setPaper('a4', 'portrait');
            return $pdf->stream($file_name . $from . '_to_' . $to . '.pdf');
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }
    
    public function GetAPIRequestdriver($url,$device_id,$token){
        $api_url = $url;
        $csrfToken = $token;
        $requestData = [
            "device_id" => $device_id,
            "token" => $csrfToken 
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
    
    public function GetAPIRequestweekly($url,$from,$to,$device_id,$token,$job_type){
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

    public function DriverGenerateReport(Request $request)
    {
        $device_id=$request->device_id;
        $token=$request->d_token;
        $this->permission->check_privilege('DRIVER_REPORT_MODULE', self::ACTION_TYPE['index']);
        $data = [];
        $data['driver_id'] = $driver_id = $request->driver_id;
        $data['report_type'] = $report_type = $request->report_type; 

        if ($request->excel == 1 && $report_type === 'Daily') {
            return $this->excel_export->driver_daily_export($request);
        } elseif ($request->excel == 1 && ($report_type === 'Monthly' || $report_type === 'Weekly' || $report_type === 'Custom')) {
            return $this->excel_export->driver_weekly_monthly_export($request);
        }

        if ($report_type === 'Monthly' || $report_type === 'Custom') {
            if($report_type === 'Monthly'){
                $data['from'] = $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
                $data['to'] = $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
                $file_name = 'driver_monthly_report_';
            } elseif($report_type === 'Custom'){
                $data['from'] = $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
                $data['to'] = $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
                $file_name = 'driver_custom_report_';
                $driver_id=$request->driver_id;
            }

            $apiUrl = env('API_URL').'driverreportmonthly';
            $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
            if ($response === false) {
                echo 'Error making the request';
            } else {
                $settled_job =$response;
            }
            // dd($settled_job);
            $all_jobs_id_string = $this->helper_service->getJobID($settled_job);
            // return $all_jobs_id_string;
        
            $all_trans_id_string = $this->helper_service->getTransID($settled_job);
            $all_jobs_id_array = explode(',', $all_jobs_id_string);

            $all_trans_id_array = explode(',', $all_trans_id_string);

            $last_trans_id = end($all_trans_id_array);

            //settle histry details
         $driver_id=$last_trans_id;   
        $apiUrl = env('API_URL').'driverreportsettlehistry';
        $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
        if ($response === false) {
            echo 'Error making the request';
        } else {
            $settle_history =$response;
        }
        $data['settle_history']=$settle_history;
            

                // get booking 
             $driver_id=$all_jobs_id_array;   
                $apiUrl = env('API_URL').'driverreportbokking';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $amount_details =$response;
                }
                $data['amount_details']=$amount_details;
                
            //job details bookings table
                 $driver_id=$all_jobs_id_array;   
                $apiUrl = env('API_URL').'driverreportbokkingdetails';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $job_details =$response;
                }
                
               // dd($job_details);
                 $data['job_details'] =$job_details;
                
        } elseif ($report_type === 'Weekly') {
            $data['from'] = $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $data['to'] = $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'driver_weekly_report_';

        $apiUrl = env('API_URL').'driverreportmonthly';
        $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
        if ($response === false) {
            echo 'Error making the request';
        } else {
            $settled_job =$response;
        }

            if (!$settled_job) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }

            
         $job_ids = [];
            foreach ($settled_job as $job) {
                $job_ids[] = $job->id;
            }   
 
         $job_ids=implode(',',$job_ids);  
         $driver_id= $job_ids; 
        $apiUrl = env('API_URL').'driverreportsettlehistry';
        $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
        if ($response === false) {
            echo 'Error making the request';
        } else {
            $settle_history =$response;
        }
          
            $data['settle_history']=$settle_history;
            

                 $driver_id=$settled_job[0]->jobid;
                $apiUrl = env('API_URL').'driverreportbokkingweekly';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $amount_details =$response;
                }
           
            $data['amount_details']=$amount_details;
            

            
                 $driver_id=$settled_job[0]->jobid;
                $apiUrl = env('API_URL').'driverreportbokkingweeklybookinks';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $job_details =$response;
                }

                     $data['job_details']=$job_details;
            
            
        } elseif ($report_type === 'Daily') {
            $data['date'] = $date = Carbon::parse($request->date_filter)->format('Y-m-d');
            $file_name = 'driver_day_report_';
                  $from='Daily';  
                  $to= $date; 
                $apiUrl = env('API_URL').'driverreportbokkingdaily';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $job_details =$response;
                }

                $data['job_details'] =$job_details;
    

                $apiUrl = env('API_URL').'driverreportbokkingdailysummary';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $summary_details =$response;
                }
                
                $data['summary_details'] = $summary_details;
                
                $apiUrl = env('API_URL').'getpartnerlogo';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $partner_lists =$response;
                }
                
                // dd($partner_lists);
                $data['partner_lists'] = $partner_lists;

            // if ($job_details->isNotEmpty()) {
                    if (!empty($job_details)) {
                $pdf = DOMPDF::loadView('reports.driver_day_report_pdf', $data)->setPaper('a4', 'portrait');
                return $pdf->stream($file_name . driver_name_for_file($driver_id) . '_' . $date . '.pdf');
            } else {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }
        }
        
                $apiUrl = env('API_URL').'getpartnerlogo';
                $response = $this->GetAPIRequestdrivertransaction($apiUrl,$driver_id,$from,$to,$device_id,$token);
                
                if ($response === false) {
                    echo 'Error making the request';
                } else {
                    $partner_lists =$response;
                }
                
                // dd($partner_lists);
                $data['partner_lists'] = $partner_lists;

                //dd($data);
        // if ($job_details->isNotEmpty()) {
                if (!empty($job_details)) {
            $pdf = DOMPDF::loadView('reports.driver_report_pdf', $data)->setPaper('a4', 'portrait');
            return $pdf->stream($file_name . driver_name_for_file($driver_id) . '_' . $from . '_to_' . $to . '.pdf');
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }
    
        public function GetAPIRequestdrivertransaction($url,$driver_id,$from,$to,$device_id,$token){
        $api_url = $url;
        $csrfToken = $token;
        $requestData = [
            "from" => $from,
            "to" => $to,
            "device_id" => $device_id,
            "token" => $csrfToken, 
            "driver_id" => $driver_id 
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
