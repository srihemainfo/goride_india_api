<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash};
use Symfony\Component\HttpFoundation\Response as HttpStatusCode;
use App\Models\Driver;
class MyridersController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $password = $request->password;
        $token = $request->token;
        $status = $request->status;
        $vech_type = $request->vech_type;
        return 'test';
                

        if (!empty($email)) {

            $driver_check = Driver::where('email', $email)->first();
            
          

            if ($driver_check) {
                
             $token_final = $driver_check->createToken('my-app-token')->plainTextToken;    

                $updateResult = $driver_check->update([
                    'token' => $token_final,
                ]);
                  $data = '{
                        "d_token": "'.$token.'",
                        "login_token": "'.$token_final.'",
                        "partner_domainname": "ecminibus.info"
                    }'; 
                                      
              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://driver.airportrides.co/api/adddriver_partner',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$data,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            
             $responseArray = json_decode($response, true);

             $message = $responseArray['message'];  

                $response = [
                    'status' => 200,
                    'message' => "Driver updated successfully",
                    'Email' => $email,
                    'Password' => $password,
                    'token' => $token_final
                ];
                
               return response()->json($response, 200);  
                
            } else {
                
                       

                $driver = Driver::create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make($password),
                    'status' => $status,
                    'vech_type' => $vech_type
                ]);
                
              $token_final = $driver->createToken('my-app-token')->plainTextToken;    

                

                $data = '{
                        "d_token": "'.$token.'",
                        "login_token": "'.$token_final.'",
                        "partner_domainname": "ecminibus.info"
                    }'; 
                    
              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://driver.airportrides.co/api/adddriver_partner',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$data,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            
             $responseArray = json_decode($response, true);

             $message = $responseArray['message'];  

                               
                 $response = [
                    'status' => 200,
                    'message' => "Driver created successfully",
                    'Email' => $email,
                    'token' => $token_final,
                    'Password' => $password
                ]; 
                
                return response()->json($response, 200);   
                 
                 
                
        }

            
            
    } else {
        
            $response = [
                'status' => 400,
                'message' => "Driver not created"
            ];

            return response()->json($response, 400);
        }
    }
    

    public function removetoken(Request $request){
        
        $driver_token=$request->token;
        $driver_email=$request->email;
        $driver_phone=$request->phone;
        
         $driver_check = Driver::where('email', $driver_email)->first();
         
         if ($driver_check) {
             
            
             $updateResult = $driver_check->update([
                    'token' => '',
               ]);
               
               $response = [
                    'status' => 200,
                    'message' => "Driver Removed successfully"
                ];
                
              return response()->json($response, 200);   
              
             
         }else{
             
             
              $response = [
                    'status' => 400,
                    'message' => "Driver Not Found"
                ];
             
          
          return response()->json($response, 400);   
             
         }     
         
        
       
        
        
    }
    
    
    
    public function checktoken(Request $request){
        
       $driver_email=$request->email;
       $partner_domainname=$request->partner_domainname;
       
       $driver_check = Driver::where('email', $driver_email)->first();

       if($driver_check){
                  $driver_token=$driver_check->token;
           
               $response = [
                    'status' => 200,
                    'message' => "Login Successfully",
                    'token' => "$driver_token"
                ];
                
              return response()->json($response, 200);   
           
       }else{
           
              $response = [
                    'status' => 400,
                    'message' => "Driver Token Not Found"
                ];
                
              return response()->json($response, 200); 
           
       }
        
    }    
    
}