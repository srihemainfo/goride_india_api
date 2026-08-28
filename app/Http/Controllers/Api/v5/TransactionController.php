<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\user_register;
// use Twilio\Rest\Client;
use GuzzleHttp\Client;
use App\Http\Controllers\Template\mailController;

use Auth;
use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


use \App\Mail\OtpMail;
class TransactionController extends Controller
{
    public function transaction_list(Request $request){
         $user_id= auth()->user()->id;
      
        //  $user_id=4747;
          $data=DB::table('user_register')->where(['id'=>$user_id,'deletes'=>0,'roll_id'=>0,'status'=>0])->first();
     
        
        $transactions =  DB::table('points_transaction')
        ->select('points_transaction.id','points_transaction.createdon','points_transaction.from_closing','points_transaction.to_closing','points_transaction.type','points_transaction.invoice_id','invoice.id as invoicid','points_transaction.points','ticket.invoice_no as invic','ticket.ticket_no',
        'ticket.payment_by','ticket.ticket_no','ticket.invoice_no','ticket.transaction_id','ticket.id as ticketid'
        )->where(function ($query) use ($user_id) {
        $query->where('from_id', $user_id)
              ->orWhere('to_id', $user_id);
    })
    ->leftjoin('invoice','invoice.id','=','points_transaction.invoice_id')
     ->leftjoin('ticket','ticket.invoice_no','=','points_transaction.invoice_id')
    ->where('points_transaction.from_id', '!=', '')
    ->where('points_transaction.deletes', '0')
    ->orderBy('id', 'DESC')
    ->get();
        
// dd($transactions);
       if($transactions){
         
    $detailsArray = [];
foreach($transactions as $transaction){
    
    // dd($transaction->type);

     if($transaction->type == "order"){
          $total_balance=$transaction->from_closing;
         $content="-AED";
         $amount= $content.' '.$transaction->points;
         //////////////////////////
         $datas=DB::table('ticket')->where(['deletes'=>'0','ticket.id'=>$transaction->invoice_id])->get();
         $description = 'Ticket ID :'.''.$datas->pluck('ticket_no')->first();

        //  dd($datas);
     }
     elseif($transaction->type == "COUPON"){
          $description = "";
          $amount="";
          $total_balance="";
     }
     
     elseif($transaction->type == "credit"){
         
         $total_balance= $transaction->to_closing;
        $amount= 'AED'.' '.$transaction->points;
        
          ////////////////////////////////////
          
        //   $transaction_id=DB::table('ticket')->select('ticket.*','invoice.id as iv')
        //   ->leftjoin('invoice','invoice.id','=','ticket.invoice_no')
        //   ->where(['ticket.deletes'=>'0','ticket.invoice_no'=>$transaction->invoice_id])->get();
        
          $transaction_id=DB::table('ticket')->select('ticket.payment_by','ticket.deletes','ticket.invoice_no')
          ->where(['ticket.deletes'=>'0','ticket.invoice_no'=>$transaction->invoice_id])->get();
        
        $test=DB::table('invoice')->select('id','response')->where('invoice.id',$transaction->invoice_no)->first();
       
                   $description_data = $transaction_id->pluck('payment_by')->first();

                          if (!empty($test->response)) {
                                  if ($description_data == "wallet") {
                                      $description='Wallet Purchase,  Invoice No'.' '.$transaction->invoice_id;
                                  } else {
                                  
                                    
                                       $payresponse = $test->response;
                                    
                            
                                      $data123 = json_decode($payresponse);
                                        if(!empty($data123->merchantOrderReference)){ 
                                            // dd($data123->_embedded->payment[0]->paymentMethod->pan);
                                        $merchantOrderReference=$data123->merchantOrderReference; 
                                        $pan=$data123->_embedded->payment[0]->paymentMethod->pan;
                                      }else{
                                          $merchantOrderReference=""; 
                                          $pan="";
                                      }
                                    
                                  
                                    $description='Reference ID'.''.$merchantOrderReference.' '.'Card:'.$pan.' '.'Invoice No. '.$transaction->invoice_id;
                                  }
                                }else{
                                   
                                    $description='Transaction Id:'.''.$transaction->transaction_id;
                                } 
         

         
     }elseif($transaction->type == "FREE"){
         $total_balance=$transaction->to_closing;
                                $content="AED";
                                $amount= $content.' '.$transaction->points;
                                ///////////////////////////////////////////////
         $datas=DB::table('fticket')->where(['deletes'=>'0','id'=>$transaction->invoice_id])->get();
        
        $ticket="Free Ticket";
         if($datas){
              $datas = $datas->pluck('transaction_id')->first();
                // dd($datas);
              $description='Free Ticket'.' '.$datas;
         }
     }
     
                              
                             
                             
        // dd($data);
   $details = [
        'id' => $transaction->id,
        'date' => date("d M Y h:i A", strtotime($transaction->createdon)),
        'type'=>$transaction->type,
      'description'=>$description,
      'amount'=>$amount,
      'total_balance'=>$total_balance,
      'winning_balance'=>$data->t_earning
       
       
    ];
     $detailsArray[] = $details;
}
    
    
    
   
            
             $response = [
                    
                  'status'=>'success',
                    'message' => 'User transaction',
                    'data' =>[
                        'winning_balance'=>$data->t_earning,
                        'transaction'=>$detailsArray,
                        
                        
                    ]
                    

                ];
                return response($response);
        }else{
                   $response = [
                  'status'=>'failed',
                    'message' => 'No data available',
                    'error'=>'no data available',

                ];
                return response($response);
        }
       

        
    }
    
     

}
