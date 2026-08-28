<?php

namespace App\Http\Controllers\Api;

use Aws\S3\S3Client;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;

class ManualKycUpload extends Controller
{
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
    
    public function deleteS3Object(Request $request)
    {
        $request->validate([
            'file_url' => 'required|string'
        ]);
    
        $path = parse_url($request->file_url, PHP_URL_PATH);
        $key = ltrim($path, '/');
    
        Storage::disk('s3')->delete($key);
        
        return response()->json([
            'status' => true,
            'message' => 'File deleted'
        ]);
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
                'address'  => ['nullable'],
                'change_status'  => ['nullable'],
                'status'   => ['nullable', 'string']
            ]);
            
            $check_kyc = DB::table('kyc_details')->where(['user_id' => $request->user_id, 'deletes' => 0])->exists();
            
            if(!$check_kyc){
                
                $ins_data = [
                    'user_id' => $request->user_id,
                    'type' => $request->type,
                    'selfie_url' => $request->s_url,
                    'selfie_status' => 'approved',
                    'o_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $ins_id = DB::table('kyc_details')->insert($ins_data);
                
                $kycId = $ins_id;
                $userId = $request->user_id;
                
                return response()->json([
                    'status'  => true,
                    'data' => 'approved',
                    'message' => 'Selfie Uploaded.'
                ], 200);
                
            }else{
                
                // if($request->change_status == 1 || ){
                 if(true){
                     
                    $ins_data = [
                        'type' => $request->type,
                        'selfie_url' => $request->s_url,
                        'selfie_status' => 'approved',
                        'o_status' => 1,
                        'updated_at' => now(),
                    ];
                    
                    $ins_id = DB::table('kyc_details')->where(['user_id' => $request->user_id, 'deletes' => 0])->update($ins_data);
                    $get_id = DB::table('kyc_details')->where(['user_id' => $request->user_id, 'deletes' => 0])->select('id')->first();
                    
                    $kycId = $get_id->id;
                    $userId = $request->user_id;
                
                    return response()->json([
                        'status'  => true,
                        'data' => 'approved',
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
    
    public function doc_verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'front_image' => 'required|url',
                'back_image'  => 'required|url',
                'type'        => 'required|string|in:AADHAAR,DRIVING_LICENSE',
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
    
                @unlink($frontTmp);
                @unlink($backTmp);
                
                $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
                // $kycId = $get_id->id;
                // $userId = auth()->user()->id;
                
                // $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                // $title = "KYC Aadhar — ". auth()->user()->name;
                
                // // data payload
                // $data = [
                //     'user_id' => $userId,
                //     'user_name' => auth()->user()->name,
                //     'kyc_id' => $kycId,
                //     'status' => $status,
                //     'changes' => null,
                // ];
                
                // NotificationService::create('kyc.updated', $title, $data, $link, $userId);
    
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
                'user_id' => 'required',
                'dl_no' => 'required',
                'exp'  => 'nullable',
                'expiry'  => 'nullable',
                'dob'  => 'nullable',
                'front_url'  => 'nullable',
                'back_url'  => 'nullable',
                'type' => 'required|string|in:DRIVING_LICENSE'
            ]);
    
            $userId = $request->user_id;
    
            if (!$userId) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Unauthenticated.'
                ], 200);
            }
    
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
                    'doc_expiry'   => $request->expiry??null,
                    'exp'   => $request->exp??null,
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
                            'exp'    => $request->exp??null,
                            'updated_at'  => now(),
                        ]
                    );
                    
                    // $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
                    // $kycId = $get_id->id;
                    // $userId = auth()->user()->id;
                    
                    // $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
                    // $title = "KYC DL — ". auth()->user()->name;
                    
                    // // data payload
                    // $data = [
                    //     'user_id' => $userId,
                    //     'user_name' => auth()->user()->name,
                    //     'kyc_id' => $kycId,
                    //     'status' => $status,
                    //     'changes' => null,
                    // ];
                    
                    // NotificationService::create('kyc.updated', $title, $data, $link, $userId);
                    
                    return response()->json([
                        'status'  => true,
                        'data'    => $status,
                        'message' => 'Driving Licence '. $status
                    ], 200);
                    
                }else{
                    DB::table('ocr_request')->where('id', $id)->update([
                        'req_response' => $n_req->getBody(),
                        'status'       => 'Request_Failed',
                    ]);
                    
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
}