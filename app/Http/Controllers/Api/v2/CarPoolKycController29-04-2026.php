<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BidPlacedToRedis;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Support\Facades\Redis;
use App\Services\NotificationService;
use App\Services\PusherService;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use App\Helpers\userLocationLog;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class CarPoolKycController extends Controller
{
    
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
    
        $extension = pathinfo($request->file_name, PATHINFO_EXTENSION);
    
        $fileName = 'carpool/' . Str::uuid() . '.' . $extension;
    
        $bucket = config('filesystems.disks.s3.bucket');
    
        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key'    => $fileName,
            'ContentType' => $request->file_type
        ]);
    
        $requestSigned = $s3Client->createPresignedRequest($cmd, '+5 minutes');
        $presignedUrl = (string) $requestSigned->getUri();
    
        return response()->json([
            'status' => true,
            'upload_url' => $presignedUrl,
            'file_url'   => "https://{$bucket}.s3.amazonaws.com/{$fileName}",
            'key'        => $fileName, // ✅ useful for DB storage
        ]);
    }
    
    public function selfieUpdate(Request $request)
    {
        
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
                'change_status'  => ['nullable'],
                'status'   => ['nullable', 'string']
            ]);
            
            $check_kyc = DB::table('kyc_carpool')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->exists();
            
            if(!$check_kyc){
            // if(true){
                
                $ins_data = [
                    'user_id' => auth()->user()->id,
                    'selfie_url' => $request->s_url,
                    'selfie_status' => 'Inreview',
                    'o_status' => 1,
                    's_lat' => $request->lat,
                    's_lang' => $request->lang,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $ins_id = DB::table('kyc_carpool')->insert($ins_data);
                
                $kycId = $ins_id;
                $userId = auth()->user()->id;
                
                $link = env('ADMIN_ENDPOINT')."car-pool/verify/{$userId}/{$kycId}";
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
                
                // if($request->change_status == 1){
                //  if(true){
                     
                    $ins_data = [
                        'selfie_url' => $request->s_url,
                        'selfie_status' => 'Inreview',
                        'o_status' => 1,
                        's_lat' => $request->lat,
                        's_lang' => $request->lang,
                        'updated_at' => now(),
                    ];
                    
                    $ins_id = DB::table('kyc_carpool')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->update($ins_data);
                    $get_id = DB::table('kyc_carpool')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->select('id')->first();
                    
                    $kycId = $get_id->id;
                    $userId = auth()->user()->id;
                    
                    $link = env('ADMIN_ENDPOINT')."car-pool/verify/{$userId}/{$kycId}";
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
                // }
                
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
    
    public function docVerify(Request $request)
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
                        'global_type' => 'customer',
                        'user_id' => $userId,
                        // 'status'  => 'approved',
                        'doc_type'  => $request->type,
                        'deletes' => 0
                    ])->whereIn('status', ['approval_pending', 'approved'])->exists();
    
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
                    'global_type' => 'customer',
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
                DB::table('kyc_carpool')->updateOrInsert(
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
                
                $get_id = DB::table('kyc_carpool')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
                $kycId = $get_id->id;
                $userId = auth()->user()->id;
                
                $link = env('ADMIN_ENDPOINT')."car-pool/verify/{$userId}/{$kycId}";
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
            \Log::info('doc_verify error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function digioRequest(Request $request)
    {
        try {
            $userId = auth()->user()->id;
    
            if ($userId != '') {
                
                $r_id = $this->generateUniqueId('reference_id', 'numeric', 10, 'digio');
                $t_id = $this->generateUniqueId('transaction_id', 'alphanumeric', 12, 'digio');
                $req_payload = [
                  "customer_identifier"=>  auth()->user()->mobile,
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
                
                
                if ($n_req->successful()) {
                    $decode_res = $n_req->json(); // returns array
                
                    $ins_req = [
                        'global_type' => 'customer',
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
            // Log::error('block_user validate error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log::error('block_user error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function docVerifyDL(Request $request)
    {
        try {
            $validated = $request->validate([
                'dl_no' => 'required',
                // 'exp'  => 'required',
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
                        'global_type' => 'customer',
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
                    'global_type' => 'customer',
                    'request_id' => $request_id,
                    'doc_type'   => $request->type,
                    'doc_no'   => $request->dl_no,
                    'doc_expiry'   => $request->expiry,
                    // 'exp'   => $request->exp,
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
                    DB::table('kyc_carpool')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'deletes' => 0
                        ],
                        [
                            'dl_status'  => $status,
                            'dl_no' => $request->dl_no,
                            'o_status'    => 3,
                            'dl_expiry' => $dl_expiry,
                            // 'exp'    => $request->exp,
                            'updated_at'  => now(),
                        ]
                    );
                    
                    $get_id = DB::table('kyc_carpool')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
                    $kycId = $get_id->id;
                    $userId = $userId;
                    
                    $link = env('ADMIN_ENDPOINT')."car-pool/verify/{$userId}/{$kycId}";
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
                    // $status   = 'approved';
                    
                    DB::table('kyc_carpool')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'deletes' => 0
                        ],
                        [
                            'dl_status'  => 'failed',
                            'dl_no' => $request->dl_no,
                            'o_status'    => 3,
                            'dl_expiry' => $request->expiry??null,
                            // 'exp'    => $request->exp,
                            'updated_at'  => now()
                        ]
                    );
                    
                    
                    // return response()->json([
                    //     'status'  => true,
                    //     'data'    => $status,
                    //     'message' => 'Driving Licence '. $status
                    // ], 200);
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => 'Failed',
                        'message' => 'Driving Licence verification failed.'
                    ], 200);
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
    
    public function rcVerify(Request $request)
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
                        'global_type' => 'customer',
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
                    'global_type' => 'customer',
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
                    
                    // return response()->json([
                    //     'status'  => true,
                    //     'data'    => 'Active',
                    //     'message' => 'RC is Active'
                    // ], 200);
                    return response()->json([
                        'status'  => false,
                        'data'    => 'Failed',
                        'message' => 'RC verification failed.'
                    ], 200);
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
    
    public function vehicleUpload(Request $request)
    {
        try {
            
            $request->validate([
                'low_pass_notify' => ['nullable'],
                'vehicle' => ['required', 'array'],
            ]);
            
            if($request->vehicle){
                $v_types = collect($request->vehicle)->pluck('type')->unique()->values()->toArray();
                $seats = json_encode($v_types);
                $request->vehicle = json_encode($request->vehicle);
            }else{
                $seats = null;
                $request->vehicle = null;
            }
            
            $userId = auth()->user()->id;
            
            DB::table('customer_register')
                ->where('id', $userId)
                ->where('deletes', '=', '0')
                ->update([
                  
                    'vehicle_details' => $request->all(),
                    'vehicle_verify' => 1
                    
                ]);
                
            $get_id = DB::table('kyc_carpool')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
            $kycId = $get_id->id;
            $userId = auth()->user()->id;
                
            $link = env('ADMIN_ENDPOINT')."car-pool/verify/{$userId}/{$kycId}";
            $title = "Vehicle Details Updated — ". auth()->user()->name;
            
            $data = [
                'user_id' => $userId,
                'user_name' => auth()->user()->name,
                'kyc_id' => $kycId,
                'status' => 'Inreview',
                'changes' => null,
            ];
            
            NotificationService::create('kyc.updated', $title, $data, $link, $userId);
            
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Vehicle Uploaded.'
            ]);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            \Log::info('vehicle upload error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
}