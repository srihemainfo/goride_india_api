<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as DOMPDF;
use Carbon\Carbon;
use App\Services\Reports\ReportsHelperService;
use App\Services\Settlements\SettlementHelperService;
use Illuminate\Support\Facades\Config;

class SettlementController extends Controller
{
    private $settlement_helper;
    private $week_array;
    private $last_week;
    private $from_date;
    private $to_date;

    public function __construct()
    {
        $this->settlement_helper = new SettlementHelperService;
        $this->week_array = (new ReportsHelperService)->getWeekArray();
        $this->helper_service = new ReportsHelperService;
        $this->last_week = current($this->week_array);
        $this->from_date = date('Y-m-d', strtotime(substr($this->last_week, 0, 10)));
        $this->to_date = date('Y-m-d', strtotime(substr($this->last_week, -10)));
    }

    public function index(Request $request)
    {
        $week_array = $this->week_array;
        $last_week = $this->last_week;
        $from_date = $this->from_date;
        $to_date = $this->to_date;

        // dd($from_date, $to_date);

        $transactions = DB::table('transaction')
            ->join('driver', 'driver.id', '=', 'transaction.driver_id')
            ->select('transaction.*', 'driver.driver_no', 'driver.name', 'driver.email')
            ->whereBetween('fromdate', [$from_date, $to_date])
            ->whereBetween('todate', [$from_date, $to_date])
            ->get();

        $transaction_summary = DB::table('transaction')
            ->whereBetween('fromdate', [$from_date, $to_date])
            ->whereBetween('todate', [$from_date, $to_date])
            ->select(
                DB::raw("SUM(transaction.total) as total"),
                DB::raw("SUM(transaction.credit) as credit"),
                DB::raw("SUM(transaction.total_credit) as total_credit")
            )
            ->get()
            ->first();
            
        $month_array = $this->helper_service->getMonthArray();
        $week_array = $this->helper_service->getWeekArray();

        return view('settlement.index', compact('transactions', 'last_week', 'week_array', 'transaction_summary', 'week_array', 'month_array'));
    }

    public function CalculateSettlemet(Request $request)
    {
        if ($request->week_filter_form) {
            $from_date = date('Y-m-d', strtotime(substr($request->week_filter_form, 0, 10)));
            $to_date = date('Y-m-d', strtotime(substr($request->week_filter_form, -10)));


            if ($this->settlement_helper->check_settlement_status($from_date, $to_date)) {
                //Recalculate settlements

                $calculated_bookings_count = 0;

                // Step 1 : Calculate any new 'Completed' booking found in bookinfo table
                if ($this->settlement_helper->is_booking_exist($from_date, $to_date)) {
                    $booking_id_array = $this->settlement_helper->get_all_bookings_id_array($from_date, $to_date);
                    $booking_ids_string = implode(", ", $booking_id_array);
                    $calculated_bookings_count += $this->settlement_helper->calculate_booking_columns($booking_ids_string);
                }

                //Step 2 : Recalculate 'settled' jobs in bookinfo table
                $settled_booking_id_array = $this->settlement_helper->get_all_bookings_id_array($from_date, $to_date, 'settled');
                $settled_booking_ids_string = implode(", ", $settled_booking_id_array);
                $calculated_bookings_count += $this->settlement_helper->calculate_booking_columns($settled_booking_ids_string);

                if($calculated_bookings_count > 0){
                    $transaction_ids = $this->settlement_helper->get_recent_transactions($from_date, $to_date)->pluck('id');

                    //Step 2 : Delete previous settlement's transaction and settle history
                    $deleted_transactions = DB::table('transaction')
                        ->where('fromdate', '=', $from_date)
                        ->where('todate', '=', $to_date)
                        ->delete();

                    $deleted_settle_histories = DB::table('settle_history')
                        ->whereIn('trans_id', $transaction_ids)
                        ->delete();

                    //Step 4 : New transaction and settle history calculation
                    $created_transactions_count = $this->settlement_helper->create_transactions($from_date, $to_date, $settled_booking_id_array);

                    return response()->json(['status' => 200, 'message' => "Caluculation done for $created_transactions_count drivers and $calculated_bookings_count bookings updated."]);
                } else {
                    return response()->json(['status' => 200, 'message' => "Everything is up to date. No new jobs or changes found for recalculation."]);
                }
            } else {
                //New settlement calculation
                if ($this->settlement_helper->is_booking_exist($from_date, $to_date)) {
                    $booking_id_array = $this->settlement_helper->get_all_bookings_id_array($from_date, $to_date);
                    $booking_ids_string = implode(", ", $booking_id_array);
                    $calculated_bookings_count = $this->settlement_helper->calculate_booking_columns($booking_ids_string);
                    $created_transactions_count = $this->settlement_helper->create_transactions($from_date, $to_date, $booking_id_array);
                    return response()->json(['status' => 200, 'message' => "Caluculation done for $created_transactions_count drivers and $calculated_bookings_count bookings."]);
                } else {
                    return response()->json(['status' => 404, 'message' => "No completed bookings found for the dates you selected."]);
                }
            }
        } else {
            return response()->json(['status' => 400, 'invalid_request' => true]);
        }
    }

    public function GetDriverNames(Request $request)
    {
        $terms = $searchstring = $request->search;

        $terms = str_replace(" ", "+", "$terms");

        $drivers_from_account = DB::table('driver')
            ->select('id', 'name')
            ->where('name', 'LIKE', '%' . $searchstring . '%')
            //->where('status', 'Active') //May be the transaction details taken for previously active driver
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        $arr = [];

        $j = 0;
        foreach ($drivers_from_account as $item) {
            $arr[] = array(
                'id' => $item->id,
                'label' => $j,
                'text' => $item->name
            );
            $j++;
        }

        $i = 6;

        return response()->json($arr);
    }

    public function GetTransactionForSettlement(Request $request)
    {
        $week_array = $this->week_array;
        //dd($data);

        if ($request->driver_id) {
            $driver_id = $request->driver_id;
        } else {
            $driver_id = "";
        }

        if ($request->week != "") {
            $data['from'] = $from = Carbon::createFromFormat('d-m-Y', substr($request->week, 0, 10))->format('Y-m-d');
            $data['to'] = $to =  Carbon::createFromFormat('d-m-Y', substr($request->week, -10))->format('Y-m-d');
            $from_date = $data['from'];
            $to_date = $data['to'];
        } else {
            $from_date = $this->from_date;
            $to_date = $this->to_date;
        }

        //dd($from_date);
        if ($driver_id == "") {

            $transactions = DB::table('transaction')
                ->join('driver', 'driver.id', '=', 'transaction.driver_id')
                ->select('transaction.*', 'driver.driver_no', 'driver.name', 'driver.email')
                ->whereBetween('fromdate', [$from_date, $to_date])
                ->whereBetween('todate', [$from_date, $to_date])
                ->get();

            $transaction_summary = DB::table('transaction')
                ->whereBetween('fromdate', [$from_date, $to_date])
                ->whereBetween('todate', [$from_date, $to_date])
                ->select(
                    DB::raw("FORMAT(SUM(transaction.total),2) as total"),
                    DB::raw("FORMAT(SUM(transaction.credit),2) as credit"),
                    DB::raw("FORMAT(SUM(transaction.total_credit),2) as total_credit")
                )
                ->get()
                ->first();
        } else {
            $transactions = DB::table('transaction')
                ->join('driver', 'driver.id', '=', 'transaction.driver_id')
                ->select('transaction.*', 'driver.driver_no', 'driver.name', 'driver.email')
                ->where('driver_id', $driver_id)
                ->whereBetween('fromdate', [$from_date, $to_date])
                ->whereBetween('todate', [$from_date, $to_date])
                ->get();

            $transaction_summary = DB::table('transaction')
                ->where('driver_id', $driver_id)
                ->whereBetween('fromdate', [$from_date, $to_date])
                ->whereBetween('todate', [$from_date, $to_date])
                ->select(
                    DB::raw("FORMAT(SUM(transaction.total),2) as total"),
                    DB::raw("FORMAT(SUM(transaction.credit),2) as credit"),
                    DB::raw("FORMAT(SUM(transaction.total_credit),2) as total_credit")
                )
                ->get()
                ->first();
        }

        return  response()->json([
            'transactions' => $transactions ? $transactions : '0' ,
            'total_week_cost' => $transaction_summary->total ? $transaction_summary->total :  '0.00',
            'total_week_settlement' => $transaction_summary->credit ? $transaction_summary->credit : '0.00',
            'total_total_settlement' => $transaction_summary->total_credit ? $transaction_summary->total_credit : '0.00',

            'from_date' => date('d-m-Y', strtotime($from_date)),
            'to_date' => date('d-m-Y', strtotime($to_date)),
        ]);
    }

    public function WeeklyDriverSettlementPdf(Request $request)
    {
        $data = [];
        $job_id = [];
        $transaction_id = $request->transaction_id;
        $driver_id = $request->driver_id;
        // $job_id = explode(",", $request->job_id);
        $job_id =$request->job_id;
        $file_name = 'Settlement Report';
        $d_token = $_COOKIE['d_token'];
        $deviced=0;

        $requestData = [
            "transaction_id" => $transaction_id,
            "driver_id" => $driver_id,
            "job_id" => $job_id,
            "file_name" => $file_name,
            "token" => $d_token,
            "deviced" => $deviced,
        ];
        
        $jsonRequestData = json_encode($requestData);
        // Make the API request using cURL
        $apiUrl = env('API_URL').'WeeklyDriverSettlemepdfdata';
        $ch = curl_init($apiUrl);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonRequestData),
            'Authorization: Bearer ' . $d_token
        ));
        $response = curl_exec($ch);
        if ($response === false) {
            echo 'Error making the request: ' . curl_error($ch);
        } else {
            $pastdraw_result = json_decode($response, true);
            $datafinal=$pastdraw_result['data'];
        
        }
        curl_close($ch);
        // dd($pastdraw_result);
        $data=$datafinal;
        $website=$data['website_details'];
        $sitecurrency=$website['currency']; 
        $partner_lists=$data['partner_list'];
        $call_currency=self::getcurrency($sitecurrency);
        $data['website_currency']=$call_currency;
        // dd($data);
        if (!empty($data)) {
            
           $pdf = DOMPDF::loadView('settlement.partials.weekly_driver_settlement_pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->stream($file_name . $transaction_id . '.pdf');
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }


        
}

public function DriverSettlementPdf(Request $request)
{
        $data = [];

        $driver_id = $request->driver_id;
        $week = $request->week;
        $data['from_date'] = $from_date = date('Y-m-d', strtotime(substr($request->week, 0, 10)));
        $data['to_date'] = $to_date = date('Y-m-d', strtotime(substr($request->week, -10)));
        //dd($from_date, $to_date);
        $file_name = 'driver_settlement_pdf_';
        $fromdate=date('Y-m-d', strtotime(substr($request->week, 0, 10)));
        $todate=date('Y-m-d', strtotime(substr($request->week, -10)));
        
         $d_token = $_COOKIE['d_token'];
        $deviced=0;

$requestData = [
    "driver_id" => $driver_id,
    "file_name" => $file_name,
    "token" => $d_token,
    "deviced" => $deviced,
    "week" => $week,
    "fromdate" => $fromdate,
    "todate" => $todate,
];

$jsonRequestData = json_encode($requestData);
// Make the API request using cURL
$apiUrl = env('API_URL').'DriverSettlementPdf';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonRequestData),
    'Authorization: Bearer ' . $d_token
));
$response = curl_exec($ch);
if ($response === false) {
    echo 'Error making the request: ' . curl_error($ch);
} else {
    $pastdraw_result = json_decode($response, true);
    

}
curl_close($ch); 
    
       $data_dats=$pastdraw_result['data'];
       $data['transactions']=$data_dats['transactions'];
       $data['transaction_summary']=$data_dats['transaction_summary'];
       $data['partner_list']=$data_dats['partner_list'];
       $website =$data_dats['website_details'];
       $sitecurrency=$website['currency']; 
       $call_currency=self::getcurrency($sitecurrency); 
       $data['website_currency']=$call_currency;   
       
       
       $pdf = DOMPDF::loadView('settlement.partials.driver_settlement_pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->stream($file_name . rand() . '.pdf');
    }

public function EmailSettlements(Request $request)
{
        $data = [];
        $transaction_id = $request->query('transaction_id');
        $driver_id = $request->query('driver_id');
        $job_id = $request->query('job_id');
        $week = $request->week;
        
        // Replace slashes (/) with dashes (-) and spaces with underscores (_)
        $formatted_week = str_replace(["/", " "], ["-", "_"], $week);
        
        // Generate the file name
        $file_name = 'Settlement_Report_' . $formatted_week . '.pdf';
        
        $d_token = $_COOKIE['d_token'];
        $deviced=0;

        $requestData = [
            "transaction_id" => $transaction_id,
            "driver_id" => $driver_id,
            "job_id" => $job_id,
            "file_name" => $file_name,
            "token" => $d_token,
            "deviced" => $deviced,
        ];
        // dd($requestData);

        $jsonRequestData = json_encode($requestData);
        // Make the API request using cURL
        $apiUrl = env('API_URL').'EmailSettlements';
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonRequestData),
            'Authorization: Bearer ' . $d_token
        ));
        $response = curl_exec($ch);
        if ($response === false) {
            echo 'Error making the request: ' . curl_error($ch);
        } else {
            $pastdraw_result = json_decode($response, true);
            
        
        }
        // dd($pastdraw_result);
        curl_close($ch);
        
        $all_details=$pastdraw_result['data']; 
        $data=$all_details;
        $website=$all_details['website_details'];
        $sitecurrency=$website['currency']; 
        $call_currency=self::getcurrency($sitecurrency);
        $data['website_currency']=$call_currency;
        $email_setting=$all_details['email_setting'];
        $from_email=$email_setting[0]['from_email'];
        if($from_email){
            $from_email=$from_email;
        }else{
          $from_email="noreply@goride.run";  
        }
        $from_name=$email_setting[0]['from_name'];
        if($from_name){
            $from_name=$from_name;
        }else{
          $from_name="Go Ride Run";  
        }

        // Load the PDF view
        $pdf = DOMPDF::loadView('settlement.partials.weekly_driver_settlement_pdf', $data)->setPaper('a4', 'portrait');
        $driver_name = $data['get_driver'][0]['name'];
        $email = $data['get_driver'][0]['email'];
        // Prepare email data
        $emailData = [
            'driver' => $driver_name,
            'week' => $week
        ];
    
        try {
            
            Config::set('mail.mailers.smtp.host', $email_setting[0]['smtp_host']); // Change host if needed
            Config::set('mail.mailers.smtp.port', $email_setting[0]['smtp_port']);
            Config::set('mail.mailers.smtp.encryption', $email_setting[0]['encryption_type']);
            Config::set('mail.mailers.smtp.username', $email_setting[0]['smtp_user_name']);
            Config::set('mail.mailers.smtp.password', $email_setting[0]['smtp_password']);
            
         $email=Mail::send('emails.driver_settlement', $emailData, function ($message) use($driver_name, $email, $week, $pdf, $file_name,$from_email,$from_name) {
                $message->from($from_email,$from_name);
                $message->to($email, $driver_name);
                $message->subject('Settlement Report - ' . $week);
                $message->attachData($pdf->output(), $file_name);
                
            });
            // return redirect()->route('dashboard')->with('message', 'Email sent successfully.');
            
            return response()->json(['status' => 200,'message' => 'Email sent successfully.']);

            
        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error('Email sending failed: ' . $e->getMessage());
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
}

    
public function getcurrency($currencyCode)
{

        try{
            
 $currencySymbols = [
        "INR"=> "₹", // Indian Rupee
        "USD"=>"$", // US Dollar
        "GBP"=>"£", // British Pound
        "EUR"=> "€", // Euro
        "JPY"=> "¥", // Japanese Yen
        "AUD"=> "A$", // Australian Dollar
        "CAD"=> "C$", // Canadian Dollar
        "CHF"=> "Fr", // Swiss Franc
        "CNY"=> "¥", // Chinese Yuan
        "RUB"=> "₽", // Russian Ruble
        // Add more currencies as needed
    ];

        return isset($currencySymbols[$currencyCode]) ? $currencySymbols[$currencyCode] : $currencyCode;
      }catch(Exception $e){
          return response()->json(['status'=>500,'error'=>$e->getmessage()]);
      }

    }


public function send_email(){
    
     $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.zeptomail.in";
        $mail->SMTPAuth = true;
        $mail->Username = ""; 
        $mail->Password = ""; 
        $mail->SMTPSecure = 'tls';  
        $mail->CharSet = 'UTF-8';  

        // Debugging settings (remove in production)
        $mail->SMTPDebug = 1;
        $mail->Debugoutput = function($str, $level) {
            echo "debug level $level; message: $str<br>";
        };

        // Recipients
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);                                 
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Attachments (if any)
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }

        // Send email
        if ($mail->send()) {
            return true;
        } else {
            return "Mail sending failed: " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
    
}

public function advanceIndex(Request $request){
    // dd('hii');
    
    return view('advancePayment.index');
}

    
    
}
