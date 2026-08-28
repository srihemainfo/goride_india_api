<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Crypt;

use PDF;



class PDFController extends Controller
{



public function getInvoicePDF(Request $request) {
       try {
                $invHTML = '';
    
            $request->invoiceId = Controller::BlockSQLInjection($request->invoiceId);
            if ($request->invoiceId == '' || $request->invoiceId == null || $request->invoiceId == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid invoice id!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }
            
        //       $userDetails = view()->shared('userDetails');
        //   $user_id =  $userDetails['userDetails']['userID'] ?? '';
           
        //     if ($user_id == '' || $user_id == null || $user_id == 'null') {
        //         $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
        //         goto returnFVI;
        //     }
            
            $invoice = DB::table('invoice')
    ->where('payment_transaction_id', 'LIKE', $request->invoiceId)
    
    //   ->where('crmID', 'LIKE',  Crypt::decrypt( $request->invoiceId))
    // ->where('user_id', $user_id)
    ->where('deletes', '0')
    ->orderBy('id', 'desc')
    // ->limit(1)
    ->get()->map(function($item) {
       $item->cart = json_decode($item->cart, true);
        
        return $item;
        
    });

// dd(Crypt::decrypt( $request->invoiceId));
            
            if ($invoice->count() < 1) {
                  $response = ['status' => 'failed', 'message' => 'Invoice Not Yet Found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }
            
            
    // dd($invoice);
    
    foreach($invoice as $key => $value) {
        
        
        $daysBetweenIncludingStart = Carbon::parse($value->startDate)->diffInDays(Carbon::parse($value->endDate));

        
        $invHTML .= '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #576</title>
</head>

<style>
    
body {
  font-family:  "DejaVu Sans" ,Arial, sans-serif;
  margin: 0;
  padding: 0;
 background-color: #fff;
}

.invoice {
  max-width: 800px;
  margin: 20px auto;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

header { 
  text-align: center;
  margin-bottom: 0;
}

header h1 {
  margin: 0;
  font-size: 2.5rem;
  color: #171f4f;
}

header p {
  margin: 5px 0;
  font-size: 1rem;
}

header h2 {
  margin-top: 5px;
  font-size: 1.5rem;
  color: #d50032;
}

.bill-to, .details, .notes, footer {
  margin-bottom: 10px;
}

h3 {
  margin-bottom: 3px;
  color: #171f4f;
}

p {
  margin: 5px 0;
  line-height: 1.5;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0;
}

table th, table td {
  /*border: 1px solid #ddd;*/
  padding: 8px;
  /*text-align: left;*/
}

.items table th, .items table td {
    border-bottom: 1px solid #ddd;
}

.items table tbody, .items table thead {
    border-right: 1px solid #ddd;
    border-left: 1px solid #ddd;
}

table th {
  background-color: #3a3a3a;
  color: #fff;
  font-weight: bold;
}

 .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                font-size: 10px;
                padding: 10px;
            } 
            
               @page {
                margin: 10mm;
            }
</style>

<body>
    <div class="invoice">
        <header>
            <table border="0">
                <tr>
                    <td align="left">
                        <img src="'. public_path('goride/img/logo-dark.png')   .'" alt="logo" style="width: 250px;">
                    </td>
                    <td align="right">
                        <h1>'. ($value->currency === 'INR' ? 'INVOICE' : 'EXPORT INVOICE') .' </h1>
                        <p>#'. $value->id .'</p>
                    </td>
                </tr>
            </table>
        </header>
        <div>
        
        
         <table border="0">
                <tr>
                    <td colspan="2" style="padding: 0;" align="left">
                        <h3>From:</h3>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;" align="left">
                        <p>Sri Hema Infotech</p>
                    </td>
                    <td style="padding: 0;text-align: right;">
                        <p><strong>Date:</strong> '. (Carbon::parse($value->createdon)->format('M j, Y')). '</p>
                    </td>
                </tr>';
                
                if($value->currency === 'INR') {
                     $invHTML .=  '<tr>
                    <td style="padding: 0;" align="left">
                        <p><strong>GST No:</strong> 33BQKPS4585F1ZG</p>
                    </td>
                </tr>';
                }
              
                
                
                
               $invHTML .=  ' 
               
            <!--   <tr>
                    <td colspan="2" style="padding: 0;" align="left">
                        <p><strong>Email ID:</strong> ' . ($value->emailid ?? '') . '</p>
                    </td>
                </tr> -->
            </table>
            
            
            
            <table border="0">
                <tr>
                    <td colspan="2" style="padding: 0;" align="left">
                        <h3>Bill To:</h3>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;" align="left">
                        <p><strong>Name:</strong> ' . ucfirst($value->firstname) . '</p>
                    </td>
                    <td style="padding: 0;text-align: right; display:none !important;" >
                        <p><strong>Date:</strong> '. (Carbon::parse($value->createdon)->format('M j, Y')). '</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;" align="left">
                        <p><strong>Mobile No:</strong> ' . ($value->mobile ?? '') .  '</p>
                    </td>
                <!--    <td style="padding: 0;text-align: right;">
                        <p><strong>Balance Due:</strong> &#8377; 000.11</p>
                    </td> -->
                </tr>
                <tr>
                    <td colspan="2" style="padding: 0;" align="left">
                        <p><strong>Email ID:</strong> ' . ($value->emailid ?? '') . '</p>
                    </td>
                </tr>
            </table>
        </div>
        <section class="items">
            <table>
                <thead>
                    <tr>
                        <th align="left">Package Name</th>
                        <th align="left">Plan Type</th>
                        <!-- <th align="left">Trial Days</th> -->
                        <th align="left">Validity</th>
                        <th align="center" colspan="2">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="left">' . ($value->cart['productDetails']['name'] ?? '') . '</td>
                        <td align="left">' . (strtolower($value->cart['planType']) === 'trail' ? 'Trial' : $value->cart['planType'] ?? '')  . '</td>
                       <!--  <td align="left">'  . (isset($value->cart['trailsDays']) ?  $value->cart['trailsDays'] . ' Day' . ($value->cart['trailsDays'] > 1 ?  's' : '' )  : '')  .   '</td> -->
                        <td align="left" colspan="2">'  . (isset($daysBetweenIncludingStart) 
    ? ($daysBetweenIncludingStart > 0 && $daysBetweenIncludingStart < 30
        ? $daysBetweenIncludingStart . ' Day' . ($daysBetweenIncludingStart > 1 ? 's' : '')
        : (intval($daysBetweenIncludingStart) >= 30
            ? round(intval($daysBetweenIncludingStart) / 30) . ' Month' . (round(intval($daysBetweenIncludingStart) / 30) > 1 ? 's' : '')
            : '')
    ) 
    : ''
)  . '</td> 
                        <td align="center">'. (Carbon::parse($value->endDate)->format('M j, Y')). '</td> 
                    </tr>
                    <tr>
                        <th colspan="5" align="left">Payment Details:</th>
                    </tr>
                    <tr>
                        <td colspan="4" align="left"><strong>Payment Method</strong></td>
                        <td align="right">' . ($value->subscription_id > 0 && $value->subscription_id != null ? 'Subscription' : '') . '</td>
                    </tr>
                    <tr>
                        <td colspan="4" align="left"><strong>Amount</strong></td>
                        <td align="right">'. ($value->currency === 'INR' ? '₹' : '$')   . ($value->totalAmt ?? 0) .'</td>
                    </tr>
                    <tr>
                        <td colspan="4" align="left"><strong>Discount</strong></td>
                        <td align="right">'. ($value->currency === 'INR' ? '₹' : '$')   . ($value->discount ?? 0) .'</td> 
                    </tr>';
                    
                    if($value->currency === 'INR'){
                      $invHTML .= '<tr>
                        <td colspan="4" align="left"><strong>GST ('. $value->taxPercentage .'%)</strong></td>
                        <td align="right">'. ($value->currency === 'INR' ? '₹' : '$')   . ($value->taxValue ?? 0) .'</td>
                    </tr>';
                    }
                    
                    
                  $invHTML .=  '<tr style="background-color: #f2f2f2;">
                        <td colspan="4" align="left"><strong>Amount Paid</strong></td>
                        <td align="right">'. ($value->currency === 'INR' ? '₹' : '$')   . ($value->grandtotal ?? 0) .'</td>
                    </tr>
                </tbody>
            </table>
        </section>
        <section class="notes" style="font-size: 12PX !important;">
        <p><strong>Note:</strong></p>
            <ul>
  
    <li>No refund or exchange.</li>
    <li>For clarifications, call or WhatsApp <a href="tel:+919884557004">919884557004</a> or email us at <a href="mailto:support@goride.run">support@goride.run</a>.</li>
    <li>For more information and Terms & Conditions, please visit our website <a href="'. $request->getHost() .'">'. $request->getHost() .'</a>.</li>
</ul>



        </section>
    </div>
    
    
     <div class="footer">
            <p>As the invoice was generated by the system, a signature is not required at this time.</p>
        </div>
</body>
</html>
        
        
    ';
    }
    
    
    
    
    $pdf = PDF::loadHTML($invHTML);

    
    return $pdf->stream($request->invoiceId  . '.pdf');  
    
    
    returnFVI:
     return redirect()->route('dashboard') 
            ->with('error', $response);
       } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
         
         
        //  dd($response);
         
         
             return redirect()->route('dashboard') 
            ->with('error', $response);
        }
    
    
}


}
