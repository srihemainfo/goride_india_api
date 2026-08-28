<?php

namespace App\Http\Controllers\Api\v5;

use Aws\S3\S3Client;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Services\NotificationService;

class DigioKyc extends Controller
{
    public function digio_callback(Request $request)
    {
         // Log raw request body (good for webhooks)
        \Log::info('Raw Request Body: ' . $request->getContent());

        // Log headers too if needed
        // \Log::info('Headers: ', $request->headers->all());

        // Log parsed input (if Laravel could parse it)
        \Log::info('Parsed Request Data: ', $request->all());
        
        if (in_array($request->event, ['kyc.request.created', 'kyc.request.completed', 'kyc.request.approved', 'kyc.request.review.ready', 'kyc.request.terminated', 'kyc.request.rejected', 'documents.pulled.successfully'])) {
            
            if($request->event == 'documents.pulled.successfully'){
                
                $get_req = DB::table('digio_request')->where('request_id', $request['payload']['digilocker_request']['kyc_request_id'])->first();
    
                if ($get_req) {
                    // Update kyc_details
                    
                    if($get_req->global_type == 'customer'){
                        $tbl = 'kyc_carpool';
                    }else{
                        $tbl = 'kyc_details';
                        
                    }
                    
                    DB::table($tbl)
                        ->where('user_id', $get_req->user_id)
                        ->where('deletes', 0)
                        ->update([
                            'proof_type' => 'AADHAR_DIGILOCKER',
                            'o_status' => $request['payload']['digilocker_request']['state'] =='COMPLETED' ? 2 : 1,
                            'proof_status' => $request['payload']['digilocker_request']['state']
                        ]);
                
                    // Update digio_request
                    DB::table('digio_request')
                        ->where('request_id', $request['payload']['digilocker_request']['kyc_request_id'])
                        ->update([
                            'status' => $request['payload']['digilocker_request']['state']
                        ]);
                        
                    // if($request['payload']['digilocker_request']['state'] == 'approval_pending'){
                        
                    //     $get_id = DB::table('kyc_details')->where(['user_id' => $get_req->user_id, 'deletes' => 0])->select('id')->first();
                    //     $u_id = DB::table('user_register')->where(['id' => $get_req->user_id, 'deletes' => 0, 'roll_id' => 0])->select('name', 'id')->first();
                        
                    //     $kycId = $get_id->id;
                    //     $userId = $get_req->user_id;
                        
                    //     $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                    //     $title = "KYC Digilocker — ". $u_id->name;
                        
                    //     // data payload
                    //     $data = [
                    //         'user_id' => $userId,
                    //         'user_name' => $u_id->name,
                    //         'kyc_id' => $kycId,
                    //         'status' => 'Inreview',
                    //         'changes' => null,
                    //     ];
                        
                    //     NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                        
                    // }
                }
                
            }else{
                
                $get_req = DB::table('digio_request')->where('request_id', $request['payload']['kyc_request']['id'])->first();
    
                if ($get_req) {
                    
                    if($get_req->global_type == 'customer'){
                        $tbl = 'kyc_carpool';
                    }else{
                        $tbl = 'kyc_details';
                        
                    }
                    
                    DB::table($tbl)
                        ->where('user_id', $get_req->user_id)
                        ->where('deletes', 0)
                        ->update([
                            'proof_type' => 'AADHAR_DIGILOCKER',
                            'o_status' => ($request['payload']['kyc_request']['status'] == 'approved' || $request['payload']['kyc_request']['status'] == 'approval_pending') ? 2 : 1,
                            'proof_status' => $request['payload']['kyc_request']['status']
                        ]);
                
                    // Update digio_request
                    DB::table('digio_request')
                        ->where('request_id', $request['payload']['kyc_request']['id'])
                        ->update([
                            'status' => $request['payload']['kyc_request']['status']
                        ]);
                        
                    // if($request['payload']['kyc_request']['status'] == 'approval_pending'){
                        
                    //     $get_id = DB::table('kyc_details')->where(['user_id' => $get_req->user_id, 'deletes' => 0])->select('id')->first();
                    //     $u_id = DB::table('user_register')->where(['id' => $get_req->user_id, 'deletes' => 0, 'roll_id' => 0])->select('name', 'id')->first();
                        
                    //     $kycId = $get_id->id;
                    //     $userId = $get_req->user_id;
                        
                    //     $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                    //     $title = "KYC Digilocker — ". $u_id->name;
                        
                    //     // data payload
                    //     $data = [
                    //         'user_id' => $userId,
                    //         'user_name' => $u_id->name,
                    //         'kyc_id' => $kycId,
                    //         'status' => 'Inreview',
                    //         'changes' => null,
                    //     ];
                        
                    //     NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                        
                    // }
                }
                
            }

        }


        // if you want to return something
        return response()->json(['status' => 'success']);
    }
    
    public function getRemoteFile($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        if(curl_errno($ch)){
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $data;
    }
    
    function generateUniqueId($column, $type = 'alphanumeric', $length = 12, $txt = null)
    {
        if($txt == 'digio'){
            
            do {
                if ($type === 'numeric') {
                    $random = str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
                } else {
                    $random = strtoupper(Str::random($length));
                }
            } while (DB::table('digio_request')->where($column, $random)->exists());
            
        }elseif($txt == 'ocr'){
            
            do {
                if ($type === 'numeric') {
                    $random = str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
                } else {
                    $random = strtoupper(Str::random($length));
                }
            } while (DB::table('ocr_request')->where($column, $random)->exists());
        }
        
    
        return $random;
    }
    
    
    public function getPresignedUrl(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
            'file_type' => 'required|string',
        ]);

        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $fileName = Str::uuid() . '.' . pathinfo($request->file_name, PATHINFO_EXTENSION);
        $bucket = config('filesystems.disks.s3.bucket');

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key'    => $fileName,
            'ContentType' => $request->file_type,
            // 'ACL'    => 'public-read',
        ]);

        $requestSigned = $s3Client->createPresignedRequest($cmd, '+5 minutes');
        $presignedUrl = (string) $requestSigned->getUri();

        return response()->json([
            'status' => true,
            'upload_url' => $presignedUrl,
            'file_url'   => "https://{$bucket}.s3.amazonaws.com/{$fileName}",
        ]);
    }
    
    
    
    public function selfie_update(Request $request){
        
        try {
            $validated = $request->validate([
                's_url' => [
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        if (in_array($request->change_status, [0, null]) && empty($value)) {
                            $fail('The ' . $attribute . ' field is required when change_status is 0 or null.');
                        }
                    },
                ],
                'type'  => ['required'],
                'address'  => ['required'],
                'change_status'  => ['nullable'],
                'status'   => ['nullable', 'string']
            ]);
            
            $check_kyc = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->exists();
            
            if(!$check_kyc){
            // if(true){
                
                $ins_data = [
                    'user_id' => auth()->user()->id,
                    'type' => $request->type,
                    'selfie_url' => $request->s_url,
                    'selfie_status' => 'Inreview',
                    'o_status' => 1,
                    's_lat' => $request->lat,
                    's_lang' => $request->lang,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $up_data = [
                    'address' => $request->address,
                    'updated_at' => now()
                ];
                
                $ins_id = DB::table('kyc_details')->insert($ins_data);
                $up_id = DB::table('user_register')->where('id', auth()->user()->id)->update($up_data);
                
                $kycId = $ins_id;
                $userId = auth()->user()->id;
                
                $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                $title = "KYC Selfie Upload — ". auth()->user()->name;
                
                // data payload
                $data = [
                    'user_id' => $userId,
                    'user_name' => auth()->user()->name,
                    'kyc_id' => $kycId,
                    'status' => 'Inreview',
                    'changes' => null,
                ];
                
                NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                
                return response()->json([
                    'status'  => true,
                    'data' => 'Inreview',
                    'message' => 'Selfie Uploaded.'
                ], 200);
                
            }else{
                
                // if($request->change_status == 1 || ){
                 if(true){
                     
                    $ins_data = [
                        'type' => $request->type,
                        'selfie_url' => $request->s_url,
                        'selfie_status' => 'Inreview',
                        'o_status' => 1,
                        's_lat' => $request->lat,
                        's_lang' => $request->lang,
                        'updated_at' => now(),
                    ];
                    
                    $up_data = [
                        'address' => $request->address,
                        'updated_at' => now()
                    ];
                    
                    $ins_id = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->update($ins_data);
                    $get_id = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->select('id')->first();
                    $up_id = DB::table('user_register')->where('id', auth()->user()->id)->update($up_data);
                    
                    $kycId = $get_id->id;
                    $userId = auth()->user()->id;
                    
                    $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                    $title = "KYC Selfie Update — ". auth()->user()->name;
                    
                    // data payload
                    $data = [
                        'user_id' => $userId,
                        'user_name' => auth()->user()->name,
                        'kyc_id' => $kycId,
                        'status' => 'Inreview',
                        'changes' => null,
                    ];
                    
                    NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                
                    return response()->json([
                        'status'  => true,
                        'data' => 'Inreview',
                        'message' => 'Selfie Uploaded.'
                    ], 200);
                }
                
                return response()->json([
                    'status'  => false,
                    'message' => 'Already User completed Selfie KYC.'
                ], 200);
            }
            
            
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('selfie_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
    }
    
    public function boot_status(Request $request){
        
        try {
            
            $get_kyc_1 = DB::table('kyc_details')
                ->where([
                    'user_id' => auth()->id(),
                    'deletes' => 0,
                ])
                ->first();
            
            $get_kyc = DB::table('kyc_details')
                ->where([
                    'user_id' => auth()->id(),
                    'deletes' => 0,
                ])
                ->first();
            
            // Get verification statuses from users table
            $doc_verify = auth()->user()->doc_verify;
            $vehicle_verify = auth()->user()->vehicle_verify;
            $re_url = null;
            $trac_id = null;
            $crm_status = 0;
            
            $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                    
            if($get_crm){
                $re_url = 'https://'.strtolower($get_crm->fullDomain);
                $crm_status = '1';
            }else{
                $get_crm_fail = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain', 'crm.subscription_id')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->id())
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', '!=', 'success')
                    ->where('crm.crmRes', 'LIKE', '%error%')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                
                if ($get_crm_fail) {
                    $crm_status = '2';
                    $trac_id = $get_crm_fail->subscription_id;
                }
                
                $get_crm_not_c = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain', 'crm.subscription_id')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->id())
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'pending')
                    ->whereNull('crm.crmRes')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                
                if ($get_crm_not_c) {
                    $crm_status = '3';
                }
            }
            
            
            // Ensure $get_kyc is always an object
            if (!$get_kyc) {
                $get_kyc = (object)[];
            }
            
            // $pop_loc = DB::table('popular_locations')
            //     ->where(['status' => '0', 'deletes' => '0'])
            //     ->pluck('name');
            
            $pop_loc = [];
                
            // Append extra fields
            $get_kyc->doc_verify = $doc_verify;
            $get_kyc->vehicle_verify = $vehicle_verify;
            
            $get_kyc->re_url = $re_url;
            $get_kyc->trac_id = $re_url;
            $get_kyc->crm_status = $crm_status;
            $get_kyc->rm_status = auth()->user()->rm_status;
            $get_kyc->rm_expiry = '2026-04-30';
            $get_kyc->popular_location = $pop_loc;
            $get_kyc->wallet_balance = auth()->user()->walletBalance;
            $get_kyc->address = auth()->user()->address;
            
            $user = auth()->user();

            if ($user && is_null($user->fcm_token) && $request->filled('fcm_token')) {
                $user->update([
                    'fcm_token' => $request->fcm_token,
                ]);
            }
            
            if(isset($request->rm_status) && $request->rm_status == '1' && auth()->user()->rm_status == '0'){
                DB::table('user_register')->where('id', auth()->id())->where('roll_id', 0)->update(['rm_status' => $request->rm_status]);
            }
            
            if(isset($request->rm_status) && $request->rm_status == '1' && auth()->user()->rm_status == '0'){
                DB::table('user_register')->where('id', auth()->id())->update(['rm_status' => $request->rm_status]);
            }
            
            $lat = round((float) $request->latitude, 7);
            $lng = round((float) $request->longitude, 7);
            $now = now();
            
            if ($lat && $lng) {
            
                $userId = auth()->id();
            
                $last = DB::table('drivers_current_location')
                    ->where('user_id', $userId)
                    ->first();
            
                if ($last) {
            
                    $distanceMoved = sqrt(
                        pow($lat - $last->lat, 2) +
                        pow($lng - $last->lng, 2)
                    );
            
                    if ($distanceMoved < 0.0005) {
                        goto GGH;
                        // return response()->json([
                        //     'status' => 'skipped',
                        //     'message' => 'Movement too small'
                        // ]);
                    }
                }
            
                DB::transaction(function () use ($userId, $lat, $lng, $now) {
            
                    DB::table('drivers_current_location')->updateOrInsert(
                        ['user_id' => $userId],
                        [
                            'lat' => $lat,
                            'lng' => $lng,
                            'updated_at' => $now
                        ]
                    );
            
                    DB::table('drivers_location_logs')->insert([
                        'user_id'     => $userId,
                        'lat'         => $lat,
                        'lng'         => $lng,
                        'recorded_at' => $now->format('Y-m-d H:i:s')
                    ]);
                });
                
                
                // $nodeUrl = env('NODE_CACHE_URL'). '/driver-location';
    
                // $response = Http::withBasicAuth(
                //     env('NODE_CACHE_USER'),
                //     env('NODE_CACHE_PASS')
                // )->withHeaders([
                //     'x-api-key' => 'GORIDE_SECRET',
                //     'Accept' => 'application/json'
                // ])
                // ->timeout(5)
                // ->post($nodeUrl, [
                //     'user_id' => $userId,
                //     'lat' => (float) $lat,
                //     'lng' => (float) $lng
                // ]);
                
                // return response()->json([
                //     'status' => 'success',
                //     'message' => 'Location updated'
                // ]);
            }
            
            GGH:
            
             $get_kyc->how_to_use = [
                'bidding_job' => 'https://www.youtube.com/watch?v=_KNRIsVe-Yw',
                'fixed_job' => 'https://www.youtube.com/watch?v=1SkQ-xmBzSY'
            ];
            
            $get_kyc->rm_terms = '
RM-Care (Relationship Manager Care) is a partner support service provided by GoRide to assist registered GoRide Partners in using the GoRide Partner App and platform features effectively.
RM-Care is an optional service and must be manually activated by the GoRide Partner through the Settings menu in the GoRide Partner App.

Under RM-Care, assigned Relationship Managers (RMs) may assist GoRide Partners with onboarding and operational guidance, including but not limited to:

GoRide Partner sign-up assistance

Document verification guidance

Uploading required documents and details

Data collection and profile completion support

Guidance on bidding for rides

Assistance in scheduling rides

Support in creating and managing ride listings

Bid management guidance

Tips and support to help GoRide Partners receive more rides

Step-by-step tutorials on using the GoRide Partner App


RM-Care support is advisory in nature only. GoRide does not guarantee ride allotment, earnings, bid acceptance, or income levels through RM-Care. All final actions, submissions, and operational decisions remain the responsibility of the GoRide Partner.

GoRide reserves the right to modify, suspend, or discontinue RM-Care services at any time due to operational, technical, or policy reasons, with or without prior notice.';

            $get_kyc->rm_privacy = "
            
When a GoRide Partner activates RM-Care, GoRide may collect and process limited personal and operational data strictly for the purpose of providing RM-Care support services. This data may include:

Partner profile information

Contact details

Vehicle and document status

App usage information related to rides, bids, and schedules

The collected information is used only to:

Assist with sign-up and verification processes

Guide GoRide Partners on app features and ride management

Provide tutorials and operational support

Improve service quality and GoRide Partner experience

RM-Care support personnel (Relationship Managers) are authorized to access only the minimum data required to perform support activities. GoRide does not sell, rent, or share GoRide Partner data with third parties for marketing or advertising purposes under RM-Care.

All RM-Care related data handling follows GoRide’s standard data protection practices and applicable laws. GoRide Partners may deactivate RM-Care at any time through the app settings, after which RM-Care access and related support activities will be discontinued.

GoRide may retain certain records for legal, compliance, and audit purposes even after RM-Care deactivation, as required by law.";

            $get_kyc->rm_why = '
            
Why Activate RM-Care?

Personal Relationship Manager support
Help with partner Queries.
Assistance in document verification
Support to upload documents & details
Profile completion guidance
Help to create rides
Support to bid for rides
Assistance to manage bids
Help to schedule rides
Guidance to get more rides
Step-by-step Partner App tutorials
            
            ';
            
            if($get_kyc_1 == null){
                
                $get_kyc = [
                    "id" => 0,
                    "user_id" => null,
                    "type" => null,
                    "selfie_url" => null,
                    "selfie_status" => null,
                    "selfie_reason" => null,
                    "proof_reason" => null,
                    "dl_reason" => null,
                    "proof_type" => null,
                    
                    "front_image" => null,
                    "rm_status" => null,
                    "back_image" => null,
                    "proof_status" => null,
                    "dl_no" => null,
                    "dl_status" => null,
                    "dl_expiry" => null,
                    "exp" => null,
                    "o_proof_type" => null,
                    "o_proof" => null,
                    "o_proof_no" => null,
                    "gst_details" => null,
                    "o_proof_status" => null,
                    "o_proof_reason" => null,
                    "address" => null,
                    "o_status" => 0,
                    "reject_reason" => null,
                    "updated_by" => null,
                    "created_at" => null,
                    "updated_at" => null,
                    "deletes" => 0,
                    "doc_verify" => 0,
                    "vehicle_verify" => 0,
                    "re_url" => null,
                    "trac_id" => null,
                    "crm_status" => 0,
                    'how_to_use' => [
                        'bidding_job' => 'https://www.youtube.com/watch?v=_KNRIsVe-Yw',
                        'fixed_job' => 'https://www.youtube.com/watch?v=1SkQ-xmBzSY'
                    ],
                    
                    "rm_terms" => '
RM-Care (Relationship Manager Care) is a partner support service provided by GoRide to assist registered GoRide Partners in using the GoRide Partner App and platform features effectively.
RM-Care is an optional service and must be manually activated by the GoRide Partner through the Settings menu in the GoRide Partner App.

Under RM-Care, assigned Relationship Managers (RMs) may assist GoRide Partners with onboarding and operational guidance, including but not limited to:

GoRide Partner sign-up assistance

Document verification guidance

Uploading required documents and details

Data collection and profile completion support

Guidance on bidding for rides

Assistance in scheduling rides

Support in creating and managing ride listings

Bid management guidance

Tips and support to help GoRide Partners receive more rides

Step-by-step tutorials on using the GoRide Partner App


RM-Care support is advisory in nature only. GoRide does not guarantee ride allotment, earnings, bid acceptance, or income levels through RM-Care. All final actions, submissions, and operational decisions remain the responsibility of the GoRide Partner.

GoRide reserves the right to modify, suspend, or discontinue RM-Care services at any time due to operational, technical, or policy reasons, with or without prior notice.',

            "rm_privacy" => "
            
When a GoRide Partner activates RM-Care, GoRide may collect and process limited personal and operational data strictly for the purpose of providing RM-Care support services. This data may include:

Partner profile information

Contact details

Vehicle and document status

App usage information related to rides, bids, and schedules

The collected information is used only to:

Assist with sign-up and verification processes

Guide GoRide Partners on app features and ride management

Provide tutorials and operational support

Improve service quality and GoRide Partner experience

RM-Care support personnel (Relationship Managers) are authorized to access only the minimum data required to perform support activities. GoRide does not sell, rent, or share GoRide Partner data with third parties for marketing or advertising purposes under RM-Care.

All RM-Care related data handling follows GoRide’s standard data protection practices and applicable laws. GoRide Partners may deactivate RM-Care at any time through the app settings, after which RM-Care access and related support activities will be discontinued.

GoRide may retain certain records for legal, compliance, and audit purposes even after RM-Care deactivation, as required by law.",

            "rm_why" => "
            
Why Activate RM-Care?

Personal Relationship Manager support
Help with partner Queries.
Assistance in document verification
Support to upload documents & details
Profile completion guidance
Help to create rides
Support to bid for rides
Assistance to manage bids
Help to schedule rides
Guidance to get more rides
Step-by-step Partner App tutorials",
                    
                    "popular_location" => [],
                    "wallet_balance" => null
                ];
            }
            
            return response()->json([
                'status'  => true,
                'data' => $get_kyc,
                'message' => 'Boot status got successfully.'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('selfie_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
    }
    
    public function new_request(Request $request)
    {
        try {
            $userId = auth()->user()->id;
    
            if ($userId != '') {
                $r_id = $this->generateUniqueId('reference_id', 'numeric', 10, 'digio');
                $t_id = $this->generateUniqueId('transaction_id', 'alphanumeric', 12, 'digio');
                $req_payload = [
                  "customer_identifier"=> auth()->user()->email,
                  "customer_name"=> auth()->user()->name,
                  "template_name"=> "DIGILOCKER KYC",
                  "notify_customer"=> false,
                  "expire_in_days"=> 1,
                  "generate_access_token"=> true,
                  "reference_id" => $r_id,
                  "transaction_id" => $t_id,
                  "generate_deeplink_info"=> true
                ];
                
                $d_url = env('DIGIO_API') . '/client/kyc/v2/request/with_template';

                $auth = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
                
                $n_req = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $auth,
                ])->post($d_url, $req_payload);
                
                // dd($n_req->body(), $d_url);
                
                if ($n_req->successful()) {
                    $decode_res = $n_req->json(); // returns array
                
                    $ins_req = [
                        'request_id'          => $decode_res['id'] ?? null,
                        'customer_name'       => $decode_res['customer_name'] ?? null,
                        'entity_id'           => $decode_res['access_token']['id'] ?? null,
                        'expire_in_days'      => $decode_res['expire_in_days'] ?? null,
                        'customer_identifier' => $decode_res['customer_identifier'] ?? null,
                        'workflow_name'       => $decode_res['workflow_name'] ?? null,
                        'status'              => $decode_res['status'] ?? null,
                        'cus_req'             => json_encode($req_payload),
                        'req_res'             => json_encode($decode_res),
                        'user_id'             => $userId,
                        "reference_id"        => $r_id,
                        "transaction_id"      => $t_id,
                        'created_at'          => now(),
                        'updated_at'          => now()
                    ];
                
                    DB::table('digio_request')->insert($ins_req);
                
                    return response()->json([
                        'status'  => true,
                        'data'    => $decode_res,
                        'message' => 'Request created successfully.'
                    ], 200);
                
                } else {
                    return response()->json([
                        'status'  => false,
                        'data'    => $n_req->json(), // include error from Digio
                        'message' => 'Request Creation Failed.'
                    ], 200);
                }
                
                
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('block_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function doc_verify2(Request $request){
        
        try {
            
           $validated = $request->validate([
                'front_image' => [
                    'required',               
                    'image',                 
                    'mimes:jpeg,png,jpg,gif',
                    'max:5120'                
                ],
                'back_image' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:5120'
                ],
                'type' => [
                    'required', 
                    'string',    
                    'in:AADHAAR,DRIVING_LICENSE'
                ],
            ]);

            
            
            $userId = auth()->user()->id;
            
            // return $userId;
    
            if ($userId != '') {
                if($request->type == 'AADHAAR'){
                    
                    $check_ocr = DB::table('ocr_request')->where(['user_id' => $userId, 'status' => 'approved', 'deletes' => 0])->exists();
                    
                    if($check_ocr){
                         return response()->json([
                            'status'  => false,
                            'data'    => [], // include error from Digio
                            'message' => 'OCR Aadhar Already Verified.'
                        ], 200);
                    }
                    
                    $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');
                    return $request_id;
                    
                    $front_image = base64_encode($this->getRemoteFile($request->file('front_image')));
                    $back_image  = base64_encode($this->getRemoteFile($request->file('back_image')));
                    
                    $ins_req = [
                        'request_id' => $request_id ?? null,
                        'doc_type'   => $request->type ?? null,
                        'front'      => $front_image ?? null,
                        'back'       => $back_image ?? null,
                        'user_id' => $userId,
                        'status'     => 'Initiated',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    $id = DB::table('ocr_request')->insertGetId($ins_req);

                    
                    $client = new Client();

                    $d_url = env('DIGIO_API') . '/v4/client/kyc/analyze/file/idcard';
                    $auth = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
                    
                    $response = $client->post($d_url, [
                        'headers' => [
                            'Authorization' => 'Basic ' . $auth,
                        ],
                        'multipart' => [
                            [
                                'name'     => 'front_part',
                                'contents' => fopen($request->file('front_image')->getPathname(), 'r'),
                                'filename' => $request->file('front_image')->getClientOriginalName(),
                            ],
                            [
                                'name'     => 'back_part',
                                'contents' => fopen($request->file('back_image')->getPathname(), 'r'),
                                'filename' => $request->file('back_image')->getClientOriginalName(),
                            ],
                            [
                                'name'     => 'unique_request_id',
                                'contents' => $request_id,
                            ],
                            [
                                'name'     => 'additional_request',
                                'contents' => json_encode([
                                    'features' => ["MASK", "CROP_ALIGN", "VERIFY", "SIGNATURE_EXTRACT", "FACE_EXTRACT", "SECURITY_FEATURE"],
                                    'expected_ids' => [$request->type],
                                    'additional_checks' => ["BLUR_IMAGE", "BLACK_AND_WHITE_IMAGE"],
                                ]),
                            ],
                        ],
                    ]);
                    
                    if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                        
                        // Success logic
                        $result = json_decode($response->getBody(), true);
                        
                        DB::table('ocr_request')->where('id', $id)->update([
                            'req_response' => $response->getBody(),
                            'status' => $result['detections'][0]['verification_result']['verified'] == true ? 'approval_pending' : 'requested' ?? null
                        ]);
                        
                        DB::table('kyc_details')->where('id', $id)->update([
                            'proof_type' => $request->type,
                            'front_image' => $front_image ?? null,
                            'back_image' => $back_image ?? null,
                            'proof_status' => $result['detections'][0]['verification_result']['verified'] == true ? 'approval_pending' : 'requested' ?? null,
                            'o_status' => 2
                        ]);
                        
                        return response()->json([
                            'status'  => true,
                            'data'    => $result['detections'][0]['verification_result']['verified'] == true ? 'approval_pending' : 'requested',
                            'message' => 'Aadhar Ocr completed.'
                        ], 200);
                        
                    } else {
                        // Failure logic
                        $result = json_decode($response->getBody(), true);
                        
                        DB::table('ocr_request')->where('id', $id)->update([
                            'req_response' => $response->getBody(),
                            'status' => 'Request_Failed'
                        ]);
                        
                        return response()->json([
                            'status'  => false,
                            'data'    => [], // include error from Digio
                            'message' => 'Aadhar Ocr failed.'
                        ], 200);
                    }
                    
                    
                }else{
                    return response()->json([
                        'status'  => false,
                        'data'    => [], // include error from Digio
                        'message' => 'Aadhar Ocr failed.'
                    ], 200);
                }
            
            }else{
                
                return response()->json([
                    'status'  => false,
                    'data'    => [], // include error from Digio
                    'message' => 'Unauthenticated.'
                ], 200);
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('block_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
    }
    
    public function doc_verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'front_image' => 'required|url',
                'back_image'  => 'required|url',
                'type'        => 'required|string|in:AADHAAR,DRIVING_LICENSE',
            ]);
    
            $userId = auth()->id();
    
            if (!$userId) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Unauthenticated.'
                ], 200);
            }
    
            if ($request->type == 'AADHAAR') {
                // Check if already verified
                $check_ocr = DB::table('ocr_request')
                    ->where([
                        'user_id' => $userId,
                        'status'  => 'approved',
                        'doc_type'  => $request->type,
                        'deletes' => 0
                    ])->exists();
    
                // if ($check_ocr) {
                if (false) {
                    return response()->json([
                        'status'  => false,
                        'data'    => [],
                        'message' => 'OCR Aadhaar already verified.'
                    ], 200);
                }
    
                // Unique request id
                $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');
    
                // Download S3 images to temporary files
                $frontTmp = tempnam(sys_get_temp_dir(), 'front_') . '.jpg';
                $backTmp  = tempnam(sys_get_temp_dir(), 'back_') . '.jpg';
    
                file_put_contents($frontTmp, $this->getRemoteFile($request->front_image));
                file_put_contents($backTmp, $this->getRemoteFile($request->back_image));
    
                // Save base64 just for DB (optional)
                // $front_image = base64_encode(file_get_contents($frontTmp));
                // $back_image  = base64_encode(file_get_contents($backTmp));
    
                // Insert request
                $id = DB::table('ocr_request')->insertGetId([
                    'request_id' => $request_id,
                    'doc_type'   => $request->type,
                    'front'      => $request->front_image,
                    'back'       => $request->back_image,
                    'user_id'    => $userId,
                    'status'     => 'Initiated',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                // Call Digio API
                $client = new Client();
                $d_url  = env('DIGIO_API') . '/v4/client/kyc/analyze/file/idcard';
                $auth   = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
    
                $response = $client->post($d_url, [
                    'headers' => [
                        'Authorization' => 'Basic ' . $auth,
                    ],
                    'multipart' => [
                        [
                            'name'     => 'front_part',
                            'contents' => fopen($frontTmp, 'r'),
                            'filename' => basename($frontTmp),
                        ],
                        [
                            'name'     => 'back_part',
                            'contents' => fopen($backTmp, 'r'),
                            'filename' => basename($backTmp),
                        ],
                        [
                            'name'     => 'unique_request_id',
                            'contents' => $request_id,
                        ],
                        [
                            'name'     => 'additional_request',
                            'contents' => json_encode([
                                'features' => ["MASK", "CROP_ALIGN", "VERIFY", "SIGNATURE_EXTRACT", "FACE_EXTRACT", "SECURITY_FEATURE"],
                                'expected_ids' => [$request->type],
                                'additional_checks' => ["BLUR_IMAGE", "BLACK_AND_WHITE_IMAGE"],
                            ]),
                        ],
                    ],
                ]);
    
                $result = json_decode($response->getBody(), true);
    
                // Extract verification result
                $verified = $result['detections'][0]['verification_result']['verified'] ?? false;
                $status   = $verified ? 'approval_pending' : 'requested';
    
                // Update request
                DB::table('ocr_request')->where('id', $id)->update([
                    'req_response' => $response->getBody(),
                    'status'       => $status,
                ]);
    
                // Update KYC details
                DB::table('kyc_details')->updateOrInsert(
                    ['user_id' => $userId], // condition
                    [
                        'proof_type'  => $request->type,
                        'front_image' => $request->front_image,
                        'back_image'  => $request->back_image,
                        'proof_status'=> $status == 'approval_pending' ? $status : null,
                        'o_status'    => $status == 'approval_pending' ? 2 : 1,
                        'updated_at'  => now(),
                    ]
                );
    
                // Cleanup temp files
                @unlink($frontTmp);
                @unlink($backTmp);
                
                $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
                $kycId = $get_id->id;
                $userId = auth()->user()->id;
                
                $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                $title = "KYC Aadhar — ". auth()->user()->name;
                
                // data payload
                $data = [
                    'user_id' => $userId,
                    'user_name' => auth()->user()->name,
                    'kyc_id' => $kycId,
                    'status' => $status,
                    'changes' => null,
                ];
                
                NotificationService::create('kyc.updated', $title, $data, $link, $userId);
    
                return response()->json([
                    'status'  => true,
                    'data'    => $status,
                    'message' => 'Aadhaar OCR completed.'
                ], 200);
            }
    
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Unsupported document type.'
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('doc_verify error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function doc_verify_dl(Request $request)
    {
        try {
            $validated = $request->validate([
                'dl_no' => 'required',
                'exp'  => 'required',
                'expiry'  => 'required',
                'dob'  => 'required',
                'front_url'  => 'nullable',
                'back_url'  => 'nullable',
                'type' => 'required|string|in:DRIVING_LICENSE'
            ]);
            
            $userId = auth()->id();
                
            if (!$userId) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Unauthenticated.'
                ], 200);
            }
            
            $name = auth()->user()->name;
    
            if ($request->type == 'DRIVING_LICENSE') {
                // Check if already verified
                $check_ocr = DB::table('ocr_request')
                    ->where([
                        'user_id' => $userId,
                        'doc_type' => $request->type,
                        'status'  => 'approved',
                        'deletes' => 0
                    ])->exists();
    
                // if ($check_ocr) {
                if (false) {
                    return response()->json([
                        'status'  => false,
                        'data'    => [],
                        'message' => 'Driving License already verified.'
                    ], 200);
                }
    
                // Unique request id
                $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');
    
                // Insert request
                $id = DB::table('ocr_request')->insertGetId([
                    'request_id' => $request_id,
                    'doc_type'   => $request->type,
                    'doc_no'   => $request->dl_no,
                    'doc_expiry'   => $request->expiry,
                    'exp'   => $request->exp,
                    'user_id'    => $userId,
                    'status'     => 'Initiated',
                    'front'     => $request->front_url??null,
                    'back'     => $request->back_url??null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                // Call Digio API
                $client = new Client();
                $d_url  = env('DIGIO_API') . '/client/v4/apis/kyc/fetch_id_data/DRIVING_LICENSE';
                $auth   = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
                
                $req_payload = [
                  "id_no"=> $request->dl_no,
                  "name"=> '',
                  "dob"=> $request->dob,
                  "file_no"=> '',
                  "is_advanced" => false,
                  "unique_request_id"=> $request_id
                ];
                
    
                $n_req = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $auth,
                ])->post($d_url, $req_payload);
                
                if ($n_req->successful()) {
                    $decode_res = $n_req->json();
                    
                    $status   = 'approved';
        
                    // Update request
                    DB::table('ocr_request')->where('id', $id)->update([
                        'req_response' => $n_req->getBody(),
                        'status'       => $status
                    ]);
                    
                    $expiry_raw = $decode_res['date_of_expiry'] ?? null;
                    
                    $dl_expiry  = $expiry_raw 
                        ? Carbon::createFromFormat('d-M-Y', $expiry_raw)->format('Y-m-d')
                        : null;
                    
                    
                    // Update KYC details
                    DB::table('kyc_details')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'deletes' => 0
                        ],
                        [
                            'dl_status'  => $status,
                            'dl_no' => $request->dl_no,
                            'o_status'    => 3,
                            'dl_expiry' => $dl_expiry,
                            'exp'    => $request->exp,
                            'updated_at'  => now(),
                        ]
                    );
                    
                    $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
                    $kycId = $get_id->id;
                    $userId = $userId;
                    
                    $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                    $title = "KYC DL — ". $name;
                    
                    // data payload
                    $data = [
                        'user_id' => $userId,
                        'user_name' => $name,
                        'kyc_id' => $kycId,
                        'status' => $status,
                        'changes' => null,
                    ];
                    
                    NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                    
                    return response()->json([
                        'status'  => true,
                        'data'    => $status,
                        'message' => 'Driving Licence '. $status
                    ], 200);
                    
                }else{
                    DB::table('ocr_request')->where('id', $id)->update([
                        'req_response' => $n_req->getBody(),
                        'status'       => 'Request_Failed'
                    ]);
                    $status   = 'approved';
                    
                    DB::table('kyc_details')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'deletes' => 0
                        ],
                        [
                            'dl_status'  => $status,
                            'dl_no' => $request->dl_no,
                            'o_status'    => 3,
                            'dl_expiry' => $request->expiry??null,
                            'exp'    => $request->exp,
                            'updated_at'  => now()
                        ]
                    );
                    
                    
                    return response()->json([
                        'status'  => true,
                        'data'    => $status,
                        'message' => 'Driving Licence '. $status
                    ], 200);
                    
                    // return response()->json([
                    //     'status'  => false,
                    //     'data'    => 'Failed',
                    //     'message' => 'Driving Licence verification failed.'
                    // ], 200);
                }
    
            }
    
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Unsupported document type.'
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('doc_verify error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function owner_proof(Request $request){
        
        try {
            $validated = $request->validate([
                'type'      => ['required', 'string'],
                'proof_url' => ['nullable', 'string', 'required_if:type,name_board'],
                'proof_no'  => ['nullable', 'string', 'required_if:type,gst'],
            ]);

            
            $check_kyc = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0, 'o_proof_type' => $request->type])->exists();
            
            // if(!$check_kyc){
            if(true){
                
                $ins_data = [
                    'o_proof_type' => $request->type,
                    'o_proof' => $request->proof_url,
                    'o_proof_no' => $request->proof_no??null,
                    'o_proof_status' => 'Inreview',
                    'type' => 'Owner',
                    'o_status' => 3,
                    'o_lat' => $request->lat,
                    'o_lang' => $request->lang,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $ins_id = DB::table('kyc_details')->where('user_id', auth()->user()->id)->update($ins_data);
                
                $get_id = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->select('id')->first();
                
                $kycId = $get_id->id;
                $userId = auth()->user()->id;
                
                $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                $title = "KYC GST / NameBoard — ". auth()->user()->name;
                
                // data payload
                $data = [
                    'user_id' => $userId,
                    'user_name' => auth()->user()->name,
                    'kyc_id' => $kycId,
                    'status' => 'Inreview',
                    'changes' => null,
                ];
                
                NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                
                return response()->json([
                    'status'  => true,
                    'data' => 'Inreview',
                    'message' => 'Document Uploaded.'
                ], 200);
                
            }else{
                
                return response()->json([
                    'status'  => false,
                    'message' => 'Already User completed Document KYC.'
                ], 200);
            }
            
            
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('selfie_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
    }
    
    public function rc_verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'seater' => 'required',
                'type' => 'required',
                'front_url' => 'required',
                'back_url' => 'required',
                'expiry' => 'nullable',
                'rc_no'  => 'required'
            ]);
    
            $userId = auth()->id();
    
            if (!$userId) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Unauthenticated.'
                ], 200);
            }
    
            if ($request->type == 'RC') {
                // Check if already verified
                $check_ocr = DB::table('ocr_request')
                    ->where([
                        'user_id' => $userId,
                        'doc_type' => $request->type,
                        'seater' => $request->seater,
                        'status'  => 'ACTIVE',
                        'deletes' => 0
                    ])->exists();
    
                // if ($check_ocr) {
                if (false) {
                    return response()->json([
                        'status'  => false,
                        'data'    => [],
                        'message' => 'RC already verified.'
                    ], 200);
                }
    
                // Unique request id
                $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');
    
                // Insert request
                $id = DB::table('ocr_request')->insertGetId([
                    'request_id' => $request_id,
                    'doc_type'   => $request->type,
                    'doc_no'   => $request->rc_no,
                    'seater'   => $request->seater,
                    'front'   => $request->front_url,
                    'back'   => $request->back_url,
                    // 'exp'   => $request->exp,
                    'user_id'    => $userId,
                    'status'     => 'Initiated',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                // Call Digio API
                $client = new Client();
                $d_url  = env('DIGIO_API') . '/client/v4/apis/verify/vehicle_rc';
                $auth   = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
                
                $req_payload = [
                  "id"=> $request->rc_no,
                  "unique_request_id"=> $request_id
                ];
                
    
                $n_req = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $auth,
                ])->post($d_url, $req_payload);
                
                // return $n_req;
                
                if ($n_req->successful()) {
                    $decode_res = $n_req->json();
                    
                    // $status = $decode_res->vehicle_details->rc_status;
                    $status = $decode_res['vehicle_details']['rc_status'];
                    $og_expiry = $decode_res['vehicle_details']['fit_up_to'] ?? null;
                    $req_expiry = $request->expiry ?? null;
                    
                    // if ($og_expiry && $req_expiry && Carbon::parse($og_expiry)->ne(Carbon::parse($req_expiry))) {
                    //     return response()->json([
                    //         'status'  => false,
                    //         'data'    => 'Failed',
                    //         'message' => 'Expiry Mismatch'
                    //     ], 200);
                    // }
        
                    // Update request
                    DB::table('ocr_request')->where('id', $id)->update([
                        'req_response' => $n_req->getBody(),
                        'status'       => $status,
                    ]);
                    
                    $details = [
                        'status' => $status,
                        'response' => $decode_res
                    ];
                    
                    return response()->json([
                        'status'  => true,
                        'data'    => $details,
                        'message' => 'RC is '. $status
                    ], 200);
                    
                }else{
                    DB::table('ocr_request')->where('id', $id)->update([
                        'req_response' => $n_req->getBody(),
                        'status'       => 'Request_Failed',
                    ]);
                    
                    return response()->json([
                        'status'  => true,
                        'data'    => 'Active',
                        'message' => 'RC is Active'
                    ], 200);
                    // return response()->json([
                    //     'status'  => false,
                    //     'data'    => 'Failed',
                    //     'message' => 'RC verification failed.'
                    // ], 200);
                }
    
            }
    
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Unsupported document type.'
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('doc_verify error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function admin_doc_verify(Request $request)
    {
    try {
        $validated = $request->validate([
            'user_id'     => 'required',
            'front_image' => 'required|url',
            'back_image'  => 'required|url',
            'type'        => 'required|string|in:AADHAAR',
        ]);

        $userId = $request->user_id;

        if (!$userId) {
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Unauthenticated.'
            ], 200);
        }

        if ($request->type == 'AADHAAR') {
            
            // 1. Directly update the KYC images and approve the document
            DB::table('kyc_details')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'proof_type'   => $request->type,
                    'front_image'  => $request->front_image,
                    'back_image'   => $request->back_image,
                    'proof_status' => 'approved', 
                    'updated_at'   => now(),
                ]
            );

            // 2. Fetch the updated KYC row to trigger the notification
            $get_id = DB::table('kyc_details')
                        ->where(['user_id' => $userId, 'deletes' => 0])
                        ->select('id')
                        ->first();
            
            if ($get_id) {
                $name = DB::table('user_register')->where('id', $userId)->value('name');
                $kycId = $get_id->id;
                
                $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                $title = "KYC Aadhar Updated — ". ($name ?? 'Driver');
                
                $data = [
                    'user_id'   => $userId,
                    'user_name' => $name,
                    'kyc_id'    => $kycId,
                    'status'    => 'approved',
                    'changes'   => null,
                ];
                
                // Keep existing notification functionality
                NotificationService::create('kyc.updated', $title, $data, $link, $userId);
            }

            // 3. Return fast success response
            return response()->json([
                'status'  => true,
                'data'    => 'approved',
                'message' => 'Aadhaar images updated successfully.'
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'data'    => [],
            'message' => 'Unsupported document type.'
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation failed.',
            'errors'  => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('doc_verify error', ['error' => $e]);
        return response()->json([
            'status'  => false,
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
}
    
    // public function admin_doc_verify(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'user_id'     => 'required',
    //             'front_image' => 'required|url',
    //             'back_image'  => 'required|url',
    //             'type'        => 'required|string|in:AADHAAR',
    //         ]);
    
    //         $userId = $request->user_id;
    
    //         if (!$userId) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'data'    => [],
    //                 'message' => 'Unauthenticated.'
    //             ], 200);
    //         }
            
    //         $name = DB::table('user_register')
    //                     ->where('id', $userId)
    //                     ->value('name');
    
    //         if ($request->type == 'AADHAAR') {
    //             $check_ocr = DB::table('ocr_request')
    //                 ->where([
    //                     'user_id' => $userId,
    //                     'status'  => 'approved',
    //                     'doc_type'  => $request->type,
    //                     'deletes' => 0
    //                 ])->exists();
    //             if (false) {
    //                 return response()->json([
    //                     'status'  => false,
    //                     'data'    => [],
    //                     'message' => 'OCR Aadhaar already verified.'
    //                 ], 200);
    //             }
    
    //             $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');
    
    //             $frontTmp = tempnam(sys_get_temp_dir(), 'front_') . '.jpg';
    //             $backTmp  = tempnam(sys_get_temp_dir(), 'back_') . '.jpg';
    
    //             file_put_contents($frontTmp, $this->getRemoteFile($request->front_image));
    //             file_put_contents($backTmp, $this->getRemoteFile($request->back_image));
    
    //             $id = DB::table('ocr_request')->insertGetId([
    //                 'request_id' => $request_id,
    //                 'doc_type'   => $request->type,
    //                 'front'      => $request->front_image,
    //                 'back'       => $request->back_image,
    //                 'user_id'    => $userId,
    //                 'status'     => 'Initiated',
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    
    //             $client = new Client();
    //             $d_url  = env('DIGIO_API') . '/v4/client/kyc/analyze/file/idcard';
    //             $auth   = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
    
    //             $response = $client->post($d_url, [
    //                 'headers' => [
    //                     'Authorization' => 'Basic ' . $auth,
    //                 ],
    //                 'multipart' => [
    //                     [
    //                         'name'     => 'front_part',
    //                         'contents' => fopen($frontTmp, 'r'),
    //                         'filename' => basename($frontTmp),
    //                     ],
    //                     [
    //                         'name'     => 'back_part',
    //                         'contents' => fopen($backTmp, 'r'),
    //                         'filename' => basename($backTmp),
    //                     ],
    //                     [
    //                         'name'     => 'unique_request_id',
    //                         'contents' => $request_id,
    //                     ],
    //                     [
    //                         'name'     => 'additional_request',
    //                         'contents' => json_encode([
    //                             'features' => ["MASK", "CROP_ALIGN", "VERIFY", "SIGNATURE_EXTRACT", "FACE_EXTRACT", "SECURITY_FEATURE"],
    //                             'expected_ids' => [$request->type],
    //                             'additional_checks' => ["BLUR_IMAGE", "BLACK_AND_WHITE_IMAGE"],
    //                         ]),
    //                     ],
    //                 ],
    //             ]);
    
    //             $result = json_decode($response->getBody(), true);
    
    //             $verified = $result['detections'][0]['verification_result']['verified'] ?? false;
    //             $status   = $verified ? 'approved' : 'requested';
    
    //             DB::table('ocr_request')->where('id', $id)->update([
    //                 'req_response' => $response->getBody(),
    //                 'status'       => $status,
    //             ]);
    
    //             @unlink($frontTmp);
    //             @unlink($backTmp);
                
    //             if($status == 'approved') {
    //                 DB::table('kyc_details')->updateOrInsert(
    //                     ['user_id' => $userId],
    //                     [
    //                         'proof_type'  => $request->type,
    //                         'front_image' => $request->front_image,
    //                         'back_image'  => $request->back_image,
    //                         'proof_status'=> $status == 'approved' ? $status : null,
    //                         'updated_at'  => now(),
    //                     ]
    //                 );
    //             } else {
    //                 return response()->json([
    //                     'status'  => false,
    //                     'message' => 'Validation failed.'
    //                 ], 422);
    //             }
                
    //             $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
    //             $kycId = $get_id->id;
    //             $userId = $request->user_id;
                
    //             $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
    //             $title = "KYC Aadhar — ". $name;
                
    //             $data = [
    //                 'user_id' => $userId,
    //                 'user_name' => $name,
    //                 'kyc_id' => $kycId,
    //                 'status' => $status,
    //                 'changes' => null,
    //             ];
                
    //             NotificationService::create('kyc.updated', $title, $data, $link, $userId);
    
    //             return response()->json([
    //                 'status'  => true,
    //                 'data'    => $status,
    //                 'message' => 'Aadhaar OCR completed.'
    //             ], 200);
    //         }
    
    //         return response()->json([
    //             'status'  => false,
    //             'data'    => [],
    //             'message' => 'Unsupported document type.'
    //         ], 200);
    
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation failed.',
    //             'errors'  => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         \Log::error('doc_verify error', ['error' => $e]);
    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
     
     public function admin_doc_verify_dl(Request $request)
     {
    try {
        $validated = $request->validate([
            'user_id'   => 'required',
            'dl_no'     => 'required',
            'expiry'    => 'required',
            'dob'       => 'required',
            'front_url' => 'nullable',
            'back_url'  => 'nullable',
            'type'      => 'required|string|in:DRIVING_LICENSE',
            'license_type' => 'nullable|string', // e.g., MCWG, LMV, LMV-TR
            'admin_id'  => 'nullable'
        ]);
        
        $userId = $request->user_id;
        $adminId = $request->admin_id ?? auth()->id() ?? 'Admin';

        if (!$userId) {
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Unauthenticated.'
            ], 200);
        }
        
        if ($request->type == 'DRIVING_LICENSE') {
            
            // 1. Format dates and setup log timestamp
            $db_expiry = \Carbon\Carbon::parse($request->expiry)->format('Y-m-d');
            $db_dob = \Carbon\Carbon::parse($request->dob)->format('Y-m-d');
            $dubaidate_time = \Carbon\Carbon::now()->format('d-m-Y h:i:s A');

            // 2. Fetch Old Data for Audit Logging
            $oldUser = DB::table('user_register')->where('id', $userId)->first();
            $oldKyc = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->orderBy('id', 'desc')->first();

            $changes = [];
            $cleanString = function($str) {
                return trim(preg_replace('/[\x00-\x1F\x7F]/u', ' ', mb_convert_encoding((string)$str, 'UTF-8', 'UTF-8'))); 
            };

            $trackChange = function($field, $old, $new) use (&$changes, $cleanString) {
                $cOld = $cleanString($old); $cNew = $cleanString($new);
                if ($cOld === '0' || strtolower($cOld) === 'null') $cOld = '';
                if ($cNew === '0' || strtolower($cNew) === 'null') $cNew = '';
                if ($cOld !== $cNew) {
                    $changes[$field] = ["old" => $cOld, "new" => $cNew];
                }
            };

            $trackChange('dob', $oldUser->dob ?? '', $db_dob);
            $trackChange('dl_no', $oldKyc->dl_no ?? '', $request->dl_no);
            $trackChange('dl_expiry', $oldKyc->dl_expiry ?? '', $db_expiry);

            // 3. Update Edit Log in user_register
            $current_log_array = json_decode($oldUser->edit_log ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($current_log_array)) $current_log_array = [];

            if (!empty($changes)) {
                $current_log_array[] = [
                    'edited_by' => $adminId,
                    'edited_at' => $dubaidate_time,
                    'details'   => $changes
                ];
            }

            DB::table('user_register')->where('id', $userId)->update([
                'dob'        => $db_dob,
                'edit_log'   => json_encode($current_log_array, JSON_HEX_APOS | JSON_HEX_QUOT),
                'updated_at' => now()
            ]);

            // 4. PREPARE EXACT JSON STRUCTURE FOR ocr_request
            $lic_type = strtoupper($request->license_type ?? 'LMV');
            $nonTransport = ['LMV', 'MCWG'];
            
            $req_response = [
                "verified" => true,
                "date_of_expiry" => $db_expiry
            ];

            if (in_array($lic_type, $nonTransport)) {
                $req_response['NT'] = $lic_type;
                $req_response['TR'] = "";
            } else {
                $req_response['TR'] = $lic_type;
                $req_response['NT'] = "";
            }

            $json_payload = json_encode($req_response);

            // 5. Update ALL existing DRIVING_LICENSE rows in ocr_request for this user
            DB::table('ocr_request')
                ->where('user_id', $userId)
                ->where('doc_type', 'DRIVING_LICENSE')
                ->update([
                    'doc_no'       => $request->dl_no,
                    'doc_expiry'   => $db_expiry,
                    'status'       => 'approved',
                    'req_response' => $json_payload,
                    'updated_at'   => now()
                ]);

            // 6. Insert a NEW entry into ocr_request (To preserve images/manual record)
            DB::table('ocr_request')->insert([
                'request_id'   => 'MANUAL_' . uniqid(),
                'doc_type'     => $request->type,
                'doc_no'       => $request->dl_no,
                'doc_expiry'   => $db_expiry,
                'user_id'      => $userId,
                'status'       => 'approved',
                'front'        => $request->front_url ?? null,
                'back'         => $request->back_url ?? null,
                'req_response' => $json_payload,
                'created_at'   => now(),
                'updated_at'   => now()
            ]);

            // 7. Update KYC Details
            DB::table('kyc_details')->updateOrInsert(
                ['user_id' => $userId, 'deletes' => 0],
                [
                    'dl_status'  => 'approved',
                    'dl_no'      => $request->dl_no,
                    'dl_expiry'  => $db_expiry,
                    'updated_at' => now(),
                ]
            );
            
            // 8. Notifications
            $kycRecord = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
            if ($kycRecord) {
                $name = $oldUser->name ?? 'Driver';
                $title = "KYC DL Updated — {$name}";
                $data = [
                    'user_id'   => $userId,
                    'user_name' => $name,
                    'kyc_id'    => $kycRecord->id,
                    'status'    => 'approved',
                    'changes'   => null,
                ];
                $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycRecord->id}";
                NotificationService::create('kyc.updated', $title, $data, $link, $userId);
            }
            
            return response()->json([
                'status'  => true,
                'data'    => 'approved',
                'message' => 'Driving Licence and OCR records updated successfully.'
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Unsupported document type.'
        ], 200);

    } catch (\Exception $e) {
        \Log::error('doc_verify_dl error', ['error' => $e]);
        return response()->json([
            'status'  => false,
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
}
     
    // public function admin_doc_verify_dl(Request $request)
    // {
//     try {
//         $validated = $request->validate([
//             'user_id' => 'required',
//             'dl_no' => 'required',
//             'expiry'  => 'required',
//             'dob'  => 'required',
//             'front_url'  => 'nullable',
//             'back_url'  => 'nullable',
//             'type' => 'required|string|in:DRIVING_LICENSE'
//         ]);
        
//         $userId = $request->user_id;

//         if (!$userId) {
//             return response()->json([
//                 'status'  => false,
//                 'data'    => [],
//                 'message' => 'Unauthenticated.'
//             ], 200);
//         }
        
//         $name = DB::table('user_register')->where('id', $userId)->value('name');

//         if ($request->type == 'DRIVING_LICENSE') {
            
//             // --- 1. SAFELY FORMAT ALL DATES ---
//             // Formats for MySQL Database (YYYY-MM-DD)
//             $db_expiry = \Carbon\Carbon::parse($request->expiry)->format('Y-m-d');
//             $db_dob = \Carbon\Carbon::parse($request->dob)->format('Y-m-d');
            
//             // Format for Digio API (DD-MM-YYYY)
//             $digio_dob = \Carbon\Carbon::parse($request->dob)->format('d-m-Y');

//             // Unique request id
//             $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');

//             // Insert request
//             $id = DB::table('ocr_request')->insertGetId([
//                 'request_id' => $request_id,
//                 'doc_type'   => $request->type,
//                 'doc_no'     => $request->dl_no,
//                 'doc_expiry' => $db_expiry, // <-- FIXED: Safely inserts YYYY-MM-DD
//                 'user_id'    => $userId,
//                 'status'     => 'Initiated',
//                 'front'      => $request->front_url ?? null,
//                 'back'       => $request->back_url ?? null,
//                 'created_at' => now(),
//                 'updated_at' => now()
//             ]);

//             // Call Digio API
//             $d_url  = env('DIGIO_API') . '/client/v4/apis/kyc/fetch_id_data/DRIVING_LICENSE';
//             $auth   = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
            
//             $req_payload = [
//               "id_no"=> $request->dl_no,
//               "name"=> '',
//               "dob"=> $digio_dob, // <-- FIXED: Safely sends DD-MM-YYYY to Digio
//               "file_no"=> '',
//               "is_advanced" => false,
//               "unique_request_id"=> $request_id
//             ];
            
//             $n_req = Http::withHeaders([
//                 'Content-Type'  => 'application/json',
//                 'Authorization' => 'Basic ' . $auth,
//             ])->post($d_url, $req_payload);
            
//             if ($n_req->successful()) {
//                 $decode_res = $n_req->json();
                
//                 $status   = 'approved';
    
//                 // Update request
//                 DB::table('ocr_request')->where('id', $id)->update([
//                     'req_response' => $n_req->getBody(),
//                     'status'       => $status
//                 ]);
                
//                 $expiry_raw = $decode_res['date_of_expiry'] ?? null;
                
//                 $dl_expiry  = $expiry_raw 
//                     ? \Carbon\Carbon::createFromFormat('d-M-Y', $expiry_raw)->format('Y-m-d')
//                     : $db_expiry; // Fallback to user input if Digio misses it
                
//                 // Update KYC details
//                 DB::table('kyc_details')->updateOrInsert(
//                     [
//                         'user_id' => $userId,
//                         'deletes' => 0
//                     ],
//                     [
//                         'dl_status'  => $status,
//                         'dl_no' => $request->dl_no,
//                         'dl_expiry' => $dl_expiry,
//                         'updated_at'  => now(),
//                     ]
//                 );

//                 // --- 2. UPDATE DOB IN USER_REGISTER ---
//                 DB::table('user_register')->where('id', $userId)->update([
//                     'dob' => $db_dob,
//                     'updated_at' => now()
//                 ]);
                
//                 $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
            
//                 $kycId = $get_id->id;
                
//                 $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
//                 $title = "KYC DL — ". $name;
                
//                 // data payload
//                 $data = [
//                     'user_id' => $userId,
//                     'user_name' => $name,
//                     'kyc_id' => $kycId,
//                     'status' => $status,
//                     'changes' => null,
//                 ];
                
//                 NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                
//                 return response()->json([
//                     'status'  => true,
//                     'data'    => $status,
//                     'message' => 'Driving Licence '. $status
//                 ], 200);
                
//             } else {
//                 DB::table('ocr_request')->where('id', $id)->update([
//                     'req_response' => $n_req->getBody(),
//                     'status'       => 'Request_Failed'
//                 ]);
                
//                 // Provide exact Digio error message to the frontend instead of generic 422
//                 $error_data = $n_req->json();
//                 $digio_error_msg = $error_data['message'] ?? 'Document verification failed at provider.';

//                 return response()->json([
//                     'status'  => false,
//                     'message' => 'Verification Failed: ' . $digio_error_msg
//                 ], 422);
//             }
//         }

//         return response()->json([
//             'status'  => false,
//             'data'    => [],
//             'message' => 'Unsupported document type.'
//         ], 200);

//     } catch (ValidationException $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Validation failed.',
//             'errors'  => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         \Log::error('doc_verify error', ['error' => $e]);
//         return response()->json([
//             'status'  => false,
//             'message' => 'Server Error: ' . $e->getMessage()
//         ], 500);
//     }
// }
    
    public function admin_rc_verify(Request $request)
    {
    try {
        $validated = $request->validate([
            'user_id'   => 'required',
            'type'      => 'required|in:RC',
            'front_url' => 'required',
            'back_url'  => 'required',
            'expiry'    => 'nullable',
            'rc_no'     => 'required',
            'admin_id'  => 'nullable' // Track which admin is making the change
        ]);

        $userId = $request->user_id;
        $adminId = $request->admin_id ?? auth()->id() ?? 'Admin'; 

        if (!$userId) {
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Unauthenticated.'
            ], 200);
        }

        // 1. Format Expiry safely if provided and set Log Time
        $db_expiry = $request->expiry ? \Carbon\Carbon::parse($request->expiry)->format('Y-m-d') : null;
        $dubaidate_time = \Carbon\Carbon::now()->format('d-m-Y h:i:s A');

        // 2. Fetch current vehicle_details and edit_log JSON
        $currentUser = DB::table('user_register')->where('id', $userId)->select('vehicle_details', 'edit_log')->first();
        
        $currentVehicleDetails = [];
        if ($currentUser && !empty($currentUser->vehicle_details)) {
            $currentVehicleDetails = json_decode($currentUser->vehicle_details, true);
            if (!is_array($currentVehicleDetails)) {
                $currentVehicleDetails = [];
            }
        }

        // 3. Track Changes Engine (Audit Log)
        $changes = [];
        $cleanString = function($str) {
            $str = mb_convert_encoding((string)$str, 'UTF-8', 'UTF-8');
            return trim(preg_replace('/[\x00-\x1F\x7F]/u', ' ', $str)); 
        };

        $trackChange = function($field, $old, $new) use (&$changes, $cleanString) {
            $cOld = $cleanString($old);
            $cNew = $cleanString($new);
            if ($cOld === '0' || strtolower($cOld) === 'null') $cOld = '';
            if ($cNew === '0' || strtolower($cNew) === 'null') $cNew = '';
            if ($cOld !== $cNew) {
                $changes[$field] = ["old" => $cOld, "new" => $cNew];
            }
        };

        // Extract old values specifically for diffing
        $old_rc_number = $currentVehicleDetails['rc_number'] ?? '';
        $old_rc_upto = $currentVehicleDetails['rc_expiry_date'] ?? ($currentVehicleDetails['rc_details']['response']['vehicle_details']['fit_up_to'] ?? '');

        // Track RC fields
        $trackChange('rc_number', $old_rc_number, $request->rc_no);
        $trackChange('rc_upto', $old_rc_upto, $db_expiry);

        // 4. Create a Mocked Response to mimic Digio so the frontend doesn't crash
        $existingRcDetails = $currentVehicleDetails['rc_details']['response']['vehicle_details'] ?? [];
        
        $mock_response = [
            'vehicle_details' => array_merge($existingRcDetails, [
                'rc_number' => $request->rc_no,
                'rc_status' => 'ACTIVE',
                'fit_up_to' => $db_expiry
            ])
        ];

        // Build exact data payload the frontend JS is expecting
        $details = [
            'status'   => 'ACTIVE',
            'response' => $mock_response
        ];

        // 5. Instantly insert into ocr_request to preserve history and images
        DB::table('ocr_request')->insert([
            'request_id'   => 'MANUAL_' . uniqid(),
            'doc_type'     => $request->type,
            'doc_no'       => $request->rc_no,
            'doc_expiry'   => $db_expiry,
            'user_id'      => $userId,
            'status'       => 'ACTIVE',
            'front'        => $request->front_url,
            'back'         => $request->back_url,
            'req_response' => json_encode($mock_response),
            'created_at'   => now(),
            'updated_at'   => now()
        ]);

        // 6. Manage Edit Log
        $current_log_array = json_decode($currentUser->edit_log ?? '[]', true);
        if (!is_array($current_log_array)) $current_log_array = [];

        if (!empty($changes)) {
            $current_log_array[] = [
                'edited_by' => $adminId,
                'edited_at' => $dubaidate_time,
                'details'   => $changes
            ];
        }
        $new_log_json = json_encode($current_log_array);

        // 7. Update PHP Array for vehicle_details
        $currentVehicleDetails['rc_number']             = $request->rc_no;
        $currentVehicleDetails['rc_status']             = true;
        $currentVehicleDetails['rc_expiry_date']        = $db_expiry;
        $currentVehicleDetails['rc_front_image_url']    = $request->front_url;
        $currentVehicleDetails['rc_back_image_url']     = $request->back_url;
        $currentVehicleDetails['rc_front_admin_status'] = 'approved';
        $currentVehicleDetails['rc_back_admin_status']  = 'approved';
        $currentVehicleDetails['rc_details']            = $details;

        // 8. Update user_register via standard update (Replaces raw JSON_SET)
        DB::table('user_register')->where('id', $userId)->update([
            'vehicle_details' => json_encode($currentVehicleDetails, JSON_UNESCAPED_SLASHES),
            'edit_log'        => $new_log_json,
            'updated_at'      => now()
        ]);

        // 9. Fast Success Response
        return response()->json([
            'status'  => true,
            'data'    => $details,
            'message' => 'RC verified successfully.'
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation failed.',
            'errors'  => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('rc_verify error', ['error' => $e]);
        return response()->json([
            'status'  => false,
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
}
    
    // public function admin_rc_verify(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'user_id' => 'required',
    //             'type' => 'required',
    //             'front_url' => 'required',
    //             'back_url' => 'required',
    //             'expiry' => 'nullable',
    //             'rc_no'  => 'required'
    //         ]);
    
    //         $userId = $request->user_id;
    
    //         if (!$userId) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'data'    => [],
    //                 'message' => 'Unauthenticated.'
    //             ], 200);
    //         }
    
    //         if ($request->type == 'RC') {
    //             // $check_ocr = DB::table('ocr_request')
    //             //     ->where([
    //             //         'user_id' => $userId,
    //             //         'doc_type' => $request->type,
    //             //         'doc_no' => $request->rc_no,
    //             //         'status'  => 'ACTIVE',
    //             //         'deletes' => 0
    //             //     ])->exists();
    
    //             // if ($check_ocr) {
    //             //     return response()->json([
    //             //         'status'  => false,
    //             //         'data'    => [],
    //             //         'message' => 'RC already verified.'
    //             //     ], 200);
    //             // }
    
    //             $request_id = $this->generateUniqueId('request_id', 'alphanumeric', 12, 'ocr');
    
    //             $id = DB::table('ocr_request')->insertGetId([
    //                 'request_id' => $request_id,
    //                 'doc_type'   => $request->type,
    //                 'doc_no'   => $request->rc_no,
    //                 'front'   => $request->front_url,
    //                 'back'   => $request->back_url,
    //                 'user_id'    => $userId,
    //                 'status'     => 'Initiated',
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]);
    
    //             $client = new Client();
    //             $d_url  = env('DIGIO_API') . '/client/v4/apis/verify/vehicle_rc';
    //             $auth   = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
                
    //             $req_payload = [
    //               "id"=> $request->rc_no,
    //               "unique_request_id"=> $request_id
    //             ];
                
    
    //             $n_req = Http::withHeaders([
    //                 'Content-Type'  => 'application/json',
    //                 'Authorization' => 'Basic ' . $auth,
    //             ])->post($d_url, $req_payload);
                
    //             if ($n_req->successful()) {
    //                 $decode_res = $n_req->json();
                    
    //                 $status = $decode_res['vehicle_details']['rc_status'];
    //                 $og_expiry = $decode_res['vehicle_details']['fit_up_to'] ?? null;
    //                 $req_expiry = $request->expiry ?? null;
        
    //                 DB::table('ocr_request')->where('id', $id)->update([
    //                     'req_response' => $n_req->getBody(),
    //                     'status'       => $status,
    //                 ]);
                    
    //                 $details = [
    //                     'status' => $status,
    //                     'response' => $decode_res
    //                 ];
                    
    //                 return response()->json([
    //                     'status'  => true,
    //                     'data'    => $details,
    //                     'message' => 'RC is '. $status
    //                 ], 200);
                    
    //             }else{
    //                 DB::table('ocr_request')->where('id', $id)->update([
    //                     'req_response' => $n_req->getBody(),
    //                     'status'       => 'Request_Failed',
    //                 ]);
                    
    //                 // return response()->json([
    //                 //     'status'  => true,
    //                 //     'data'    => 'Active',
    //                 //     'message' => 'RC is Active'
    //                 // ], 200);
                    
    //                 return response()->json([
    //                     'status'  => false,
    //                     'message' => 'Validation failed.'
    //                 ], 422);
    //             }
    
    //         }
    
    //         return response()->json([
    //             'status'  => false,
    //             'data'    => [],
    //             'message' => 'Unsupported document type.'
    //         ], 200);
    
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation failed.',
    //             'errors'  => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         \Log::error('doc_verify error', ['error' => $e]);
    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage()
    //         ], 500); 
    //     }
    // }
    
    public function admin_new_request(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required'
            ]);
            
            $userId = $request->user_id;
    
            if ($userId != '') {
                
                $name = DB::table('user_register')
                        ->where('id', $userId)
                        ->value('name');
                
                $email = DB::table('user_register')
                        ->where('id', $userId)
                        ->value('email');
                
                $r_id = $this->generateUniqueId('reference_id', 'numeric', 10, 'digio');
                $t_id = $this->generateUniqueId('transaction_id', 'alphanumeric', 12, 'digio');
                $req_payload = [
                  "customer_identifier"=> $email,
                  "customer_name"=> $name,
                  "template_name"=> "DIGILOCKER KYC",
                  "notify_customer"=> false,
                  "expire_in_days"=> 1,
                  "generate_access_token"=> true,
                  "reference_id" => $r_id,
                  "transaction_id" => $t_id,
                  "generate_deeplink_info"=> true
                ];
                
                $d_url = env('DIGIO_API') . '/client/kyc/v2/request/with_template';

                $auth = base64_encode(env('DIGIO_CLIENT_ID') . ':' . env('DIGIO_CLIENT_SECERT'));
                
                $n_req = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $auth,
                ])->post($d_url, $req_payload);
                
                if ($n_req->successful()) {
                    $decode_res = $n_req->json();
                
                    $ins_req = [
                        'request_id'          => $decode_res['id'] ?? null,
                        'customer_name'       => $decode_res['customer_name'] ?? null,
                        'entity_id'           => $decode_res['access_token']['id'] ?? null,
                        'expire_in_days'      => $decode_res['expire_in_days'] ?? null,
                        'customer_identifier' => $decode_res['customer_identifier'] ?? null,
                        'workflow_name'       => $decode_res['workflow_name'] ?? null,
                        'status'              => $decode_res['status'] ?? null,
                        'cus_req'             => json_encode($req_payload),
                        'req_res'             => json_encode($decode_res),
                        'user_id'             => $userId,
                        "reference_id"        => $r_id,
                        "transaction_id"      => $t_id,
                        'created_at'          => now(),
                        'updated_at'          => now()
                    ];
                
                    DB::table('digio_request')->insert($ins_req);
                
                    return response()->json([
                        'status'  => true,
                        'data'    => $decode_res,
                        'message' => 'Request created successfully.'
                    ], 200);
                
                } else {
                    return response()->json([
                        'status'  => false,
                        'data'    => $n_req->json(),
                        'message' => 'Request Creation Failed.'
                    ], 200);
                }
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('block_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}