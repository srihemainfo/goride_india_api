<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Models\{Invoice};
use Illuminate\Support\Facades\{Validator, DB, Mail};
use Barryvdh\DomPDF\Facade\Pdf as DOMPDF;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;
use Illuminate\Support\Facades\Config;

class InvoiceController extends Controller
{
    private $module = 'INVOICE_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index(Request $request){
       
         $cookieString = $request->headers->get('cookie');
         $cookieArray = explode('; ', $cookieString);
        $dToken = null;
        foreach ($cookieArray as $cookie) {
            if (strpos($cookie, 'd_token') !== false) {
                $dToken = substr($cookie, strpos($cookie, '=') + 1);
                break;
            }
        }
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        if ($request->ajax()) {
        $data = [
            "token" => $dToken,
            "device_id" => 0,
        ];
        $queryString = http_build_query($data);
        $apiUrl = env('API_URL').'invoice?' . $queryString;
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        if ($result === false) {
            echo 'Error making the request';
        } else {
            $pastdraw_result = json_decode($result, true);
        }
            $data=$pastdraw_result;
    
    // dd($data);


        return DataTables::of(collect($data))
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                $filter_from_date = $request->has('filter_from_date') ? date('Y-m-d', strtotime($request->get('filter_from_date'))) : '';
                $filter_to_date = $request->has('filter_to_date') ? date('Y-m-d', strtotime($request->get('filter_to_date'))) : '';
                // if ($request->has('invoiceno')) {
                //     $query->where('invoiceno', 'like', "%{$request->get('invoiceno')}%");
                // }

                // if ($filter_from_date && $filter_to_date) {
                //     $query->whereBetween('invdate', [$filter_from_date, $filter_to_date]);
                // }

            })
            ->editColumn('invdate', function ($data) {
                return date('d-M-Y', strtotime($data['invdate']));
            })
            ->editColumn('clientname', function ($data) {
                //{!! 'first line <br> second line' !!}

               return $data['clientname'] ."<br><address>". $data['clientaddress'] ."</address>";

            })
            ->editColumn('jobid', function ($data) {
                return implode(" ", explode(",",$data['jobid']));
            })

            // ->addColumn('status', function ($row) use ($IS_UPDATABLE) {
            //     $status = '';
            //     if(false){
                    
            //         if ($IS_UPDATABLE) {
            //             if ($row['payment_status'] === 'Paid') {
            //                 $status = "<select class=\"form-control invoice-status\" name=\"status\" data-id=\"$row[id]\" data-previous=\"$row[payment_status]\"> <option value=\"Paid\">Paid</option> <option value=\"Pending\">Pending</option></select>";
            //             } elseif ($row['payment_status'] === 'Pending') {
            //                 $status = "<select class=\"form-control invoice-status\" name=\"status\" data-id=\"$row[id]\" data-previous=\"$row[payment_status]\"> <option value=\"Pending\">Pending</option> <option value=\"Paid\">Paid</option> </select>";
            //             }
            //         } else {
            //             if ($row['status'] === 'Paid') {
            //                 $status = $row['status'];
            //             } elseif ($row['status'] === 'Pending') {
            //                 $status = $row['status'];
            //             }
            //         }
            //         return $status;
                    
            //     }else{
            //         if ($IS_UPDATABLE) {
            //             if ($row['status'] === 'Paid') {
            //                 $status = "<select class=\"form-control invoice-status\" name=\"status\" data-id=\"$row[id]\" data-previous=\"$row[status]\"> <option value=\"Paid\">Paid</option> <option value=\"Pending\">Pending</option></select>";
            //             } elseif ($row['status'] === 'Pending') {
            //                 $status = "<select class=\"form-control invoice-status\" name=\"status\" data-id=\"$row[id]\" data-previous=\"$row[status]\"> <option value=\"Pending\">Pending</option> <option value=\"Paid\">Paid</option> </select>";
            //             }
            //         } else {
            //             if ($row['status'] === 'Paid') {
            //                 $status = $row['status'];
            //             } elseif ($row['status'] === 'Pending') {
            //                 $status = $row['status'];
            //             }
            //         }
            //         return $status;
            //     }
                
            // })

           ->addColumn('action', function ($row) use ($IS_CREATABLE, $IS_DELETABLE) {
                $btn = '';
                if ($IS_DELETABLE) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row['id'] . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteInvoice"><i class="fa fa-trash"></i></a>';
                }
                if ($IS_CREATABLE) {
                    $btn = $btn . '<a href="'. route('InvoiceGenerateReport').'?invoice_no='.$row['invoiceno'].'" target="_blank" data-id="' . $row['id'] . '" title="Preview Invoice" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-info previewInvoice"><i class="fa fa-print"></i></a>';
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row['invoiceno'] . '" title="Send Email" class="email_now mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendEmail"><i class="fa fa-envelope"></i></a>';
                }
                return $btn;
            })

            ->rawColumns(['clientname', 'action'])
            ->make(true);

    }

    return view('invoice.index', compact('IS_CREATABLE'));
}

    public function InvoiceStatusUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $data = Invoice::updateOrCreate(['id' => $request->id], ['status' => $request->status]);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }

    public function CancelInvoice(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $invoiceno = 'INP'.$request->invoiceno;
        
     $cookieString = $request->headers->get('cookie');
     $cookieArray = explode('; ', $cookieString);
    $dToken = null;
    foreach ($cookieArray as $cookie) {
        if (strpos($cookie, 'd_token') !== false) {
            $dToken = substr($cookie, strpos($cookie, '=') + 1);
            break;
        }
    }      
        
        

    $data = [
    "token" => $dToken,
    "device_id" => 0,
    "invoiceno" => $invoiceno
];
$queryString = http_build_query($data);
$apiUrl = env('API_URL').'CancelInvoice?' . $queryString;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

if ($result === false) {
    echo 'Error making the request';
} else {
    $pastdraw_result = json_decode($result, true);
}
    $data=$pastdraw_result;

$booking_cancel_update=$data;


        return response()->json($booking_cancel_update ? ['status' => 200, 'data' => $booking_cancel_update] : ['status' => 400, 'data' => $booking_cancel_update]);

    }

    public function InvoiceGenerateReport(Request $request)
    {
        
        $data = [];
        $invoice_no = $request->invoice_no;
        $file_name = 'invoice_report_';
        
    $cookieString = $request->headers->get('cookie');
    $cookieArray = explode('; ', $cookieString);
    $dToken = null;
    foreach ($cookieArray as $cookie) {
        if (strpos($cookie, 'd_token') !== false) {
            $dToken = substr($cookie, strpos($cookie, '=') + 1);
            break;
        }
    }

    $data = [
    "token" => $dToken,
    "device_id" => 0,
    "invoice_no" =>$invoice_no,
    ];
    
$queryString = http_build_query($data);
$apiUrl = env('API_URL').'InvoiceGenerateReport?' . $queryString;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

if ($result === false) {
    echo 'Error making the request';
} else {
    $pastdraw_result = json_decode($result, true);
}
    $data=$pastdraw_result;
    $website=$data['website_details'];
    $sitecurrency=$website['currency'];
    $call_currency=self::getcurrency($sitecurrency);    
    $data['website_currency']=$call_currency;    
        // dd($data);
        $pdf = DOMPDF::loadView('invoice.invoice_report_pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->stream($file_name . $invoice_no . '.pdf');
    }

    public function create(Request $request)
    {
        return view('invoice.create');
    }

    public function GetClientNames(Request $request)
    {
        $terms = $searchstring = $request->search;

        $terms = str_replace(" ", "+", "$terms");

        $drivers_from_account = DB::table('account')
            ->select('id','f_name')
            ->where('f_name', 'LIKE', '%' . $searchstring . '%')
            ->where('status', 'Active')
            ->orderBy('f_name','asc')
            ->limit(10)
            ->get();

        $arr = [];

        $j = 0;
        foreach ($drivers_from_account as $item) {
            $arr[] = array(
                'id' => $item->id,
                'label' => $j,
                'text' => $item->f_name
            );
            $j++;
        }

        $i = 6;

        return response()->json($arr);
    }


    public function GetJobNos(Request $request)
    {
        $terms = $searchstring = $request->search;

        $terms = str_replace(" ", "+", "$terms");

        $jobs_from_booking = DB::table('bookinfo')
            ->select('job_no')
            ->where('job_no', 'LIKE', '%' . $searchstring . '%')
            ->where(function ($query) {
                $query->where('inv_id','=','')
                    ->orWhereNull('inv_id');
            })
            ->whereIn('order_status',['settled','Completed', 'Confirmed'])
            ->orderBy('job_no','desc')
            ->limit(15)
            ->get();

        $arr = [];

        $j = 0;
        foreach ($jobs_from_booking as $item) {
            $arr[] = array(
                'id' => $item->job_no,
                'label' => $j,
                'text' => $item->job_no
            );
            $j++;
        }

        $i = 6;


        return response()->json($arr);
    }


    public function GetBookingForInvoice(Request $request)
    {
        //dd($request->all());
        // // $data = DB::table('bookinfo')->select('*')
        //     ->where(function($query) use($request){
        //         if($request->filled('cus_id')){
        //             $query->where('user_id', $request->cus_id);
        //         }

        //         if($request->filled('job_no')){
        //             $query->whereIn('job_no', $request->job_no);
        //         }

        //         if($request->filled('from_date') && $request->filled('to_date')){
        //             $query->whereBetween('pickup_date', [$request->from_date, $request->to_date]);
        //         }
        //     })
        //     ->where(function ($query) {
        //         $query->where('inv_id','=','')
        //             ->orWhereNull('inv_id');
        //     })
        //     ->whereIn('order_status',['settled','Completed'])
        //     ->get();
        
        

        return  response()->json([
            'booking_details' => $data,
        ]);
    }

    public function GenerateInvoice(Request $request)
    {
        $invoice_totals = DB::table('bookinfo')
            ->whereIn('job_no', $request->selected_booking)
            ->sum('total');

        $invoice_details = DB::table('bookinfo')
            ->select('*')
            ->whereIn('job_no', $request->selected_booking)
            ->get();

        $driver_name = DB::table('bookinfo')
            ->select('fname')
            ->whereIn('job_no', $request->selected_booking)
            ->get();
        $driver_name =$driver_name->first()->fname;

        $user_id = DB::table('bookinfo')
            ->select('user_id')
            ->whereIn('job_no', $request->selected_booking)
            ->get();

        $user_id = $user_id->first()->user_id;

        $last_invoice_id = DB::table('invoiceno')
            ->orderby('id', 'desc')
            ->select('id')
            ->limit(1)
            ->get();

        $last_invoice_id =$last_invoice_id->first()->id;
        $new_invoice_id =$last_invoice_id + 1;
        $new_invoice_no ='INP'.$new_invoice_id;

        //dd($driver_name);

        return  response()->json([
            'invoice_totals' => $invoice_totals,
            'invoice_details' => $invoice_details,
            'new_invoice_no' => $new_invoice_no,
            'driver_name' => $driver_name,
            'user_id' => $user_id,
        ]);
    }

    public function StoreInvoice(Request $request){
        //dd($request);

        $validator = Validator::make($request->all(), [
            "driver_name" => ["required"],
            "driver_id" => ["required", "numeric"],
            "invoice_no" => ["required"],
            "driver_address" => ["required"],
            "invoice_date" => ["required"],
            "payment_type" => ["required"],
            "status" => ["required"],

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            $data = Invoice::updateOrCreate(
                ['id' => $request->invoiceno_id],
                [
                    "memberid" => $request->driver_id,
                    "jobid" => $request->selected_jobs_str,
                    "clientname" => $request->driver_name,
                    "clientaddress" => $request->driver_address,
                    "invdate" => $request->invoice_date,
                    "invoiceno" => $request->invoice_no,
                    "pay_type" => $request->payment_type,
                    "net" => $request->net,
                    "tax_per" => $request->tax_per,
                    "tax_amt" => $request->tax_amt,
                    "total" => $request->total,
                    "date_time" => $request->date_time,
                    "status" => $request->status,
                ]
            );
                        //dd($booking_invoice_update);
            if($data->id){
                DB::table('bookinfo')
                    ->whereIn('job_no', $request->selected_jobs)
                    ->update(['inv_id' => $request->invoice_no,
                                'inv_amt' => $request->net]);

                $request->session()->put('invoice_save', 'Invoice generated successfully.');
            }

            $redirect_url ='/invoice';
            return response()->json($data->id ? ['status' => 200, 'redirect_url' => $redirect_url, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }


//     public function EmailInvoice(Request $request){
        
//         $data = [];
//         $invoice_no = $request->invoice_no;
//         $file_name = 'invoice_report_'.$invoice_no.'.pdf';
        
//     $cookieString = $request->headers->get('cookie');
//     $cookieArray = explode('; ', $cookieString);
//     $dToken = null;
//     foreach ($cookieArray as $cookie) {
//         if (strpos($cookie, 'd_token') !== false) {
//             $dToken = substr($cookie, strpos($cookie, '=') + 1);
//             break;
//         }
//     }
        
        
//     $data = [
//     "token" => $dToken,
//     "device_id" => 0,
//     "invoice_no" =>$invoice_no,
//     ];
    
// $queryString = http_build_query($data);
// $apiUrl = '{{env('API_URL')}}EmailInvoice?' . $queryString;

// $ch = curl_init($apiUrl);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//     'Content-Type: application/json'
// ));
// $result = curl_exec($ch);
// if (curl_errno($ch)) {
//     echo 'Error:' . curl_error($ch);
// }
// curl_close($ch);

// if ($result === false) {
//     echo 'Error making the request';
// } else {
//     $pastdraw_result = json_decode($result, true);
// }
//   $data=$pastdraw_result;   
//   $booking_deatails=$data['booking_details'];
//   $website=$data['website_details'];
//   $sitecurrency=$website['currency'];
//   $call_currency=self::getcurrency($sitecurrency);    
//   $data['website_currency']=$call_currency;   
//   $email_setting=$data['email_setting'];
//   $from_email=$email_setting[0]['from_email'];
//     if($from_email){
//         $from_email=$from_email;
//     }else{
//       $from_email="noreply@goride.run";  
//     }
//     $from_name=$email_setting[0]['from_name'];
//     if($from_name){
//         $from_name=$from_name;
//     }else{
//       $from_name="Go Ride Run";  
//     }

//   $pdf = DOMPDF::loadView('invoice.invoice_report_pdf', $data)->setPaper('a4', 'portrait');
  
 
//         $customer = $booking_deatails[0]['fname'];
//         $email = $booking_deatails[0]['email'];
//         $emailData = [
//             'customer' => $customer,
//             'invoice_no' => $invoice_no

//         ];

//         try {
            

            
//       Mail::send('emails.customer_invoice', $emailData, function ($message) use ($customer, $email, $pdf, $file_name, $from_email, $from_name, $week) {
//     $message->from($from_email, $from_name);
//     $message->to($email, $customer);
//     $message->subject('Weekly Settlement Report - ' . $week);
//     $message->attachData($pdf->output(), $file_name);
// });

// // Optionally handle mail failures
// if (Mail::failures()) {
//     // Log or handle the error appropriately
//     return response()->json(['message' => 'Failed to send email.'], 500);
// }

// return response()->json(['message' => 'Email sent successfully.']);

            
//         } catch (\Throwable $e) {
//             return response()->json(['status' => 400, 'message' => 'Failed to sent email.']);
//         }
//     }

public function EmailInvoice(Request $request)
{
    $data = [];
    $invoice_no = $request->invoice_no;
    $file_name = 'invoice_report_' . $invoice_no . '.pdf';

    // Extract d_token from cookies
    $cookieString = $request->headers->get('cookie');
    $cookieArray = explode('; ', $cookieString);
    $dToken = null;
    foreach ($cookieArray as $cookie) {
        if (strpos($cookie, 'd_token') !== false) {
            $dToken = substr($cookie, strpos($cookie, '=') + 1);
            break;
        }
    }

    // Prepare the data for the API request
    $data = [
        "token" => $dToken,
        "device_id" => 0,
        "invoice_no" => $invoice_no,
    ];

    // Make the API request using curl
    $queryString = http_build_query($data);
    $apiUrl = env('API_URL').'EmailInvoice?' . $queryString;

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return response()->json(['message' => 'Error making the request: ' . curl_error($ch)], 500);
    }
    curl_close($ch);

    if ($result === false) {
        return response()->json(['message' => 'Error making the request'], 500);
    } else {
        $pastdraw_result = json_decode($result, true);
    }
    // Process the response data
    $data = $pastdraw_result;
    // dd($data);
    $booking_details = $data['booking_details'];
    $website = $data['website_details'];
    $sitecurrency = $website['currency'];
    $call_currency = self::getcurrency($sitecurrency);
    $data['website_currency'] = $call_currency;
    
    $email_setting = $data['email_setting'];
    $from_email = $email_setting[0]['from_email'] ?? "noreply@goride.run";
    $from_name = $email_setting[0]['from_name'] ?? "Go Ride Run";
    
    // Generate PDF using DOMPDF
    $pdf = \PDF::loadView('invoice.invoice_report_pdf', $data)->setPaper('a4', 'portrait');

    // Prepare email data
    $customer = $booking_details[0]['fname'];
    $email = $booking_details[0]['email'];
    $week = 'Week Placeholder';
    $emailData = [
        'customer' => $customer,
        'invoice_no' => $invoice_no
    ];
    // dd($customer);
    try {
        
        Config::set('mail.mailers.smtp.host', $email_setting[0]['smtp_host']); // Change host if needed
        Config::set('mail.mailers.smtp.port', $email_setting[0]['smtp_port']);
        Config::set('mail.mailers.smtp.encryption', $email_setting[0]['encryption_type']);
        Config::set('mail.mailers.smtp.username', $email_setting[0]['smtp_user_name']);
        Config::set('mail.mailers.smtp.password', $email_setting[0]['smtp_password']);
        
        Mail::send('emails.customer_invoice', $emailData, function ($message) use ($customer, $email, $pdf, $file_name, $from_email, $from_name, $week) {
            $message->from($from_email, $from_name);
            $message->to($email, $customer);
            $message->subject('Weekly Settlement Report - ' . $week);
            $message->attachData($pdf->output(), $file_name);
        });

        return response()->json(['message' => 'Email sent successfully.']);

    } catch (\Throwable $e) {
        return response()->json(['status' => 400, 'message' => 'Failed to send email. Error: ' . $e->getMessage()], 400);
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
    
    public function temp_invoiceIndex(Request $request)
    {
        $data = [];
        $book_id = '';
        $book_status = '';
        $job_no = '';
        
        $file_name = 'invoice_report_';
        
        $cookieString = $request->headers->get('cookie');
        $cookieArray = explode('; ', $cookieString);
        $dToken = null;
        foreach ($cookieArray as $cookie) {
            if (strpos($cookie, 'd_token') !== false) {
                $dToken = substr($cookie, strpos($cookie, '=') + 1);
                // break;
            }
            
            if(strpos($cookie, 'invoice_booking') !== false){
                $invoice_booking = json_decode(substr($cookie, strpos($cookie, '=') + 1));
                
                if($invoice_booking){
                    
                    $book_id = $invoice_booking->id;
                    $book_status = $invoice_booking->job_no;
                    $job_no = $invoice_booking->job_status;
                    break;
                    
                }
            }
        }
    
        $data = [
            "token" => $dToken,
            "device_id" => 0,
            "book_id" =>$book_id,
            "job_no" =>$job_no,
            "book_status" =>$book_status
        ];
        // dd($data);
        // $queryString = http_build_query($data);
        // $apiUrl = env('API_URL').'temp-invoice?' . $queryString;
        
        // $ch = curl_init($apiUrl);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/json'
        // ));
        
        $apiUrl = env('API_URL') . 'view-temp-invoice';

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        
        $pastdraw_result = '';
        // dd($result);
        if ($result === false) {
            echo 'Error making the request';
        } else {
            $pastdraw_result = json_decode($result, true);
        }
        
        $data = $pastdraw_result;
        // dd($data);
        $pdf = DOMPDF::loadView('invoice.temp_invoice', ['data' => $data])->setPaper('a4', 'portrait');
        return $pdf->stream($file_name . '_1' . '.pdf');
    }

    
}
