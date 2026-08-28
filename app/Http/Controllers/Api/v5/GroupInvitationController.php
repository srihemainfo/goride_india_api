<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Razorpay\Api\Api;

class GroupInvitationController extends Controller
{
    
    public $serviceAccountPath;
    public $serviceAccount;
    public $serviceAccountPath2;
    public $serviceAccount2;
    public $razorpay;
    
    public function __construct()
    {
        
        $this->razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
        
        $this->serviceAccountPath = storage_path('app/firebase/firebase-config-customer.json');
        
        $this->serviceAccountPath2 = storage_path('app/firebase/firebase-config-customer-schedule.json');
    
        if (!file_exists($this->serviceAccountPath)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
        }
        
        if (!file_exists($this->serviceAccountPath2)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
        }
        
    
        $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        $this->serviceAccount2 = json_decode(file_get_contents($this->serviceAccountPath2), true);
    }
    
    public function getAccessToken()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));
    
        $now = time();
        $claimSet = [
            'iss'   => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => $this->serviceAccount['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600
        ];
    
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput  = "$header.$claimSetEncoded";
    
        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount['private_key']),
            OPENSSL_ALGO_SHA256
        );
    
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Request access token
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);
    
        $ch = curl_init($this->serviceAccount['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }
    
    public function getAccessToken2()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));
    
        $now = time();
        $claimSet = [
            'iss'   => $this->serviceAccount2['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => $this->serviceAccount2['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600
        ];
    
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput  = "$header.$claimSetEncoded";
    
        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount2['private_key']),
            OPENSSL_ALGO_SHA256
        );
    
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Request access token
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);
    
        $ch = curl_init($this->serviceAccount2['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }
    
    public function sendFCM($accessToken, $fcmToken, $title, $body, $data = [])
    {
        // Ensure all data values are strings
        $stringData = [];
        foreach ($data as $key => $value) {
            $validKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
            $stringData[$validKey] = (string) $value;
        }
        
        $stringData['title'] = $title;
        $stringData['body'] = $body;
    
        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->serviceAccount['project_id'] . '/messages:send';
    
        $payload = [
            'validate_only' => false,
            'message' => [
                'token' => $fcmToken,
                'notification' => [ // 👈 required for mobile push
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'ttl' => '86400s',
                    'notification' => [
                        'channel_id' => 'new_job_channel',
                        'sound' => 'custom_notification',
                        'color' => '#FF6B35',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'sound' => 'custom_notification.wav',
                            'badge' => 1
                        ]
                    ]
                ],
                'data' => $stringData
                
            ]
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        curl_close($ch);
    
        return json_decode($result, true);
    }
    
    public function generateInviteLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'group_id' => 'required|integer'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ],422);
    
        }
    
        DB::beginTransaction();
    
        try{
    
            $user = auth()->user();
    
            $group = DB::table('society_groups')
                ->where('id',$request->group_id)
                ->whereNull('deleted_at')
                ->first();
    
            if(!$group){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Group not found.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            $permission = DB::table('group_members')
                ->where('group_id',$group->id)
                ->where('user_id',$user->id)
                ->whereIn('role',['owner','admin'])
                ->where('status','approved')
                ->exists();
    
            if(!$permission){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Permission denied.',
                    'data'=>(object)[]
                ],403);
    
            }
    
            do{
    
                $token = Str::random(40);
    
                $exists = DB::table('group_invitations')
    
                    ->where('invite_token',$token)
    
                    ->exists();
    
            }while($exists);
    
            DB::table('group_invitations')
    
                ->insert([
                    'group_id'=>$group->id,
                    'invited_by'=>$user->id,
                    'invite_type'=>'link',
                    'invite_token'=>$token,
                    'expired_at'=>now()->addDays(30),
                    'status'=>'pending',
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
                'message'=>'Invite link generated successfully.',
                'data'=>[
                    'invite_link'=>url('/group/join/'.$token),
                    'expired_at'=>now()->addDays(30)
                ]
            ]);
    
        }catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to generate invite link.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function joinByInviteLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'invite_code' => 'required|string|max:50',
    
            'join_type' => 'required|in:invite_link'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()->first(),
                'data'=>(object)[]
            ],422);
    
        }
    
        DB::beginTransaction();
    
        try{
    
            $user = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Invite Link
            |--------------------------------------------------------------------------
            */
    
            $invite = DB::table('group_invite_links')
    
                ->where('invite_code',$request->invite_code)
    
                ->where('status','active')
    
                ->first();
    
            if(!$invite){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Invalid invite link.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Expiry
            |--------------------------------------------------------------------------
            */
    
            if($invite->expired_at &&
                Carbon::parse($invite->expired_at)->isPast()){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Invite link expired.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Usage Limit
            |--------------------------------------------------------------------------
            */
    
            if(
                $invite->max_usage > 0 &&
                $invite->used_count >= $invite->max_usage
            ){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Invite link usage exceeded.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Already Member
            |--------------------------------------------------------------------------
            */
    
            $member = DB::table('group_members')
    
                ->where('group_id',$invite->group_id)
    
                ->where('user_id',$user->id)
    
                ->where('status','approved')
    
                ->exists();
    
            if($member){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Already joined.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Pending Request
            |--------------------------------------------------------------------------
            */
    
            $pending = DB::table('group_join_requests')
    
                ->where('group_id',$invite->group_id)
    
                ->where('user_id',$user->id)
    
                ->where('status','pending')
    
                ->exists();
    
            if($pending){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Join request already submitted.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Create Request
            |--------------------------------------------------------------------------
            */
    
            $requestId = DB::table('group_join_requests')
    
                ->insertGetId([
    
                    'group_id'=>$invite->group_id,
    
                    'invite_link_id'=>$invite->id,
    
                    'user_id'=>$user->id,
    
                    'requested_by'=>'invite_link',
    
                    'status'=>'pending',
    
                    'created_at'=>now(),
    
                    'updated_at'=>now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | Update Usage
            |--------------------------------------------------------------------------
            */
    
            // DB::table('group_invite_links')
    
            //     ->where('id',$invite->id)
    
            //     ->increment('used_count');
    
            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            */
    
            /*
                Notify Owner
    
                Push Notification
    
                Firebase
    
                Socket
    
            */
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Join request submitted successfully.',
    
                'data'=>[
    
                    'request_id'=>$requestId,
    
                    'status'=>'pending'
    
                ]
    
            ]);
    
        }
        catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to process request.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function inviteDetails($inviteCode)
    {
        try {
    
            $user = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Invite Link
            |--------------------------------------------------------------------------
            */
    
            $invite = DB::table('group_invite_links as gil')
    
                ->join('society_groups as sg', 'sg.id', '=', 'gil.group_id')
    
                ->leftJoin('group_categories as gc', 'gc.id', '=', 'sg.category_id')
    
                ->leftJoin('customer_register as cr', 'cr.id', '=', 'sg.created_by')
    
                ->select(
    
                    'gil.id as invite_link_id',
                    'gil.group_id',
                    'gil.invite_code',
                    'gil.max_usage',
                    'gil.used_count',
                    'gil.expired_at',
                    'gil.status as invite_status',
    
                    'sg.id',
                    'sg.group_uuid',
                    'sg.name',
                    'sg.description',
                    'sg.image',
                    'sg.privacy_type',
                    'sg.member_count',
                    'sg.job_count',
    
                    'gc.name as category_name',
    
                    'cr.id as owner_id',
                    'cr.name as owner_name',
                    'cr.profile_img_url as owner_image'
    
                )
    
                ->where('gil.invite_code', $inviteCode)
    
                ->where('gil.status', 'active')
    
                ->whereNull('sg.deleted_at')
    
                ->first();
    
            if (!$invite) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid invite link.',
                    'data' => (object)[]
                ], 404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Expired
            |--------------------------------------------------------------------------
            */
    
            if (!empty($invite->expired_at) && Carbon::parse($invite->expired_at)->isPast()) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Invite link has expired.',
                    'data' => (object)[]
                ], 422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Usage Limit
            |--------------------------------------------------------------------------
            */
    
            if (
                $invite->max_usage > 0 &&
                $invite->used_count >= $invite->max_usage
            ) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Invite link usage limit exceeded.',
                    'data' => (object)[]
                ], 422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Already Member
            |--------------------------------------------------------------------------
            */
    
            $isMember = DB::table('group_members')
    
                ->where('group_id', $invite->group_id)
    
                ->where('user_id', $user->id)
    
                ->where('status', 'approved')
    
                ->exists();
    
            /*
            |--------------------------------------------------------------------------
            | Pending Request
            |--------------------------------------------------------------------------
            */
    
            $pendingRequest = DB::table('group_join_requests')
    
                ->where('group_id', $invite->group_id)
    
                ->where('user_id', $user->id)
    
                ->where('status', 'pending')
    
                ->exists();
    
            /*
            |--------------------------------------------------------------------------
            | Owner
            |--------------------------------------------------------------------------
            */
    
            $isOwner = DB::table('group_members')
    
                ->where('group_id', $invite->group_id)
    
                ->where('user_id', $user->id)
    
                ->where('role', 'owner')
    
                ->where('status', 'approved')
    
                ->exists();
    
            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Invite details fetched successfully.',
    
                'data' => [
    
                    'group' => [
    
                        'id' => $invite->group_id,
                        'uuid' => $invite->group_uuid,
                        'name' => $invite->name,
                        'description' => $invite->description,
                        'image' => $invite->image,
                        'category' => $invite->category_name,
                        'privacy_type' => $invite->privacy_type,
                        'member_count' => $invite->member_count,
                        'job_count' => $invite->job_count
    
                    ],
    
                    'owner' => [
    
                        'id' => $invite->owner_id,
                        'name' => $invite->owner_name,
                        'profile_img_url' => $invite->owner_image
    
                    ],
    
                    'invite' => [
    
                        'invite_code' => $invite->invite_code,
                        'expired_at' => $invite->expired_at,
                        'used_count' => $invite->used_count,
                        'max_usage' => $invite->max_usage
    
                    ],
    
                    'user' => [
    
                        'already_member' => $isMember,
                        'pending_request' => $pendingRequest,
                        'is_owner' => $isOwner,
                        'can_join' => (!$isMember && !$pendingRequest)
    
                    ]
    
                ]
    
            ]);
    
        } catch (\Exception $e) {
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to fetch invite details.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    }
    
    public function joinRequests(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'group_id' => 'required|integer',
    
            'page' => 'nullable|integer|min:1',
    
            'per_page' => 'nullable|integer|min:1|max:100',
    
            'search' => 'nullable|string|max:100'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ], 422);
    
        }
    
        try {
    
            $user = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Permission
            |--------------------------------------------------------------------------
            */
    
            $permission = DB::table('group_members')
    
                ->where('group_id', $request->group_id)
    
                ->where('user_id', $user->id)
    
                ->whereIn('role', ['owner', 'admin'])
    
                ->where('status', 'approved')
    
                ->first();
    
            if (!$permission) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Permission denied.',
                    'data' => (object)[]
                ], 403);
    
            }
    
            $page = $request->page ?? 1;
    
            $perPage = $request->per_page ?? 20;
    
            $offset = ($page - 1) * $perPage;
    
            /*
            |--------------------------------------------------------------------------
            | Query
            |--------------------------------------------------------------------------
            */
    
            $query = DB::table('group_join_requests as gjr')
    
                ->join('customer_register as c', 'c.id', '=', 'gjr.user_id')
    
                ->where('gjr.group_id', $request->group_id)
    
                ->where('gjr.status', 'pending');
    
            if (!empty($request->search)) {
    
                $query->where(function ($q) use ($request) {
    
                    $q->where('c.name', 'LIKE', '%' . trim($request->search) . '%')
    
                        ->orWhere('c.mobile', 'LIKE', '%' . trim($request->search) . '%');
    
                });
    
            }
    
            $total = (clone $query)->count();
    
            $requests = $query
    
                ->select(
    
                    'gjr.id',
    
                    'gjr.user_id',
    
                    'gjr.requested_by',
    
                    'gjr.created_at as requested_at',
    
                    'c.name',
    
                    'c.mobile',
    
                    'c.profile_img_url'
    
                )
    
                ->orderBy('gjr.created_at', 'DESC')
    
                ->offset($offset)
    
                ->limit($perPage)
    
                ->get();
    
            foreach ($requests as $row) {
    
                if (!empty($row->profile_img_url)) {
    
                    $row->profile_img_url = Storage::disk('s3')->url($row->profile_img_url);
    
                }
    
            }
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Join requests fetched successfully.',
    
                'data' => [
    
                    'pagination' => [
    
                        'current_page' => (int)$page,
    
                        'per_page' => (int)$perPage,
    
                        'total_records' => $total,
    
                        'total_pages' => ceil($total / $perPage)
    
                    ],
    
                    'requests' => $requests
    
                ]
    
            ]);
    
        } catch (\Exception $e) {
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to fetch join requests.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    }
    
    public function approveMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'request_id' => 'required|integer'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ], 422);
    
        }
    
        DB::beginTransaction();
    
        try {
    
            $loginUser = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Join Request
            |--------------------------------------------------------------------------
            */
    
            $joinRequest = DB::table('group_join_requests')
    
                ->where('id', $request->request_id)
    
                ->where('status', 'pending')
    
                ->first();
    
            if (!$joinRequest) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Join request not found.',
                    'data' => (object)[]
                ], 404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Permission
            |--------------------------------------------------------------------------
            */
    
            $permission = DB::table('group_members')
    
                ->where('group_id', $joinRequest->group_id)
    
                ->where('user_id', $loginUser->id)
    
                ->whereIn('role', ['owner', 'admin'])
    
                ->where('status', 'approved')
    
                ->first();
    
            if (!$permission) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Permission denied.',
                    'data' => (object)[]
                ], 403);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Already Member
            |--------------------------------------------------------------------------
            */
    
            $alreadyMember = DB::table('group_members')
    
                ->where('group_id', $joinRequest->group_id)
    
                ->where('user_id', $joinRequest->user_id)
    
                ->where('status', 'approved')
    
                ->exists();
    
            if ($alreadyMember) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'User already joined this group.',
                    'data' => (object)[]
                ], 422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Add Member
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_members')->insert([
    
                'group_id'   => $joinRequest->group_id,
    
                'user_id'    => $joinRequest->user_id,
    
                'role'       => 'member',
    
                'status'     => 'approved',
    
                'joined_at'  => now(),
    
                'created_at' => now(),
    
                'updated_at' => now()
    
            ]);
    
            /*
            |--------------------------------------------------------------------------
            | Approve Join Request
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_join_requests')
    
                ->where('id', $joinRequest->id)
    
                ->update([
    
                    'status'      => 'approved',
    
                    'approved_by' => $loginUser->id,
    
                    'approved_at' => now(),
    
                    'updated_at'  => now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | Update Member Count
            |--------------------------------------------------------------------------
            */
    
            DB::table('society_groups')
    
                ->where('id', $joinRequest->group_id)
    
                ->increment('member_count');
    
            /*
            |--------------------------------------------------------------------------
            | Update Invite Link Usage
            |--------------------------------------------------------------------------
            */
    
            if (!empty($joinRequest->invite_link_id)) {
    
                DB::table('group_invite_links')
    
                    ->where('id', $joinRequest->invite_link_id)
    
                    ->increment('used_count');
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            */
    
            /*
                Send Push Notification
    
                Welcome Notification
    
                Create System Chat Message
    
                Activity Log
    
                Analytics
    
            */
            
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$joinRequest->group_id}";
    
            DB::commit();
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Member approved successfully.',
    
                'data' => (object)[],
                'topic' => $topic
    
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to approve member.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    
    }
    
    public function rejectMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'request_id' => 'required|integer',
    
            'reason' => 'nullable|string|max:500'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ],422);
    
        }
    
        DB::beginTransaction();
    
        try{
    
            $loginUser = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Join Request
            |--------------------------------------------------------------------------
            */
    
            $joinRequest = DB::table('group_join_requests')
    
                ->where('id',$request->request_id)
    
                ->where('status','pending')
    
                ->first();
    
            if(!$joinRequest){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Join request not found.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Permission
            |--------------------------------------------------------------------------
            */
    
            $permission = DB::table('group_members')
    
                ->where('group_id',$joinRequest->group_id)
    
                ->where('user_id',$loginUser->id)
    
                ->whereIn('role',['owner','admin'])
    
                ->where('status','approved')
    
                ->exists();
    
            if(!$permission){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Permission denied.',
                    'data'=>(object)[]
                ],403);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Reject Request
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_join_requests')
    
                ->where('id',$joinRequest->id)
    
                ->update([
    
                    'status'=>'rejected',
    
                    'approved_by'=>$loginUser->id,
    
                    'approved_at'=>now(),
    
                    'rejection_reason'=>$request->reason,
    
                    'updated_at'=>now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            */
    
            /*
                Push Notification
    
                Firebase
    
                Activity Log
    
                Email
    
            */
    
            DB::commit();
            
            $fcm = $loginUser->fcm_token;
            
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$joinRequest->group_id}";
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebase->manageTopicSubscription('unsubscribe', $fcm, $topic);
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Join request rejected successfully.',
    
                'data'=>(object)[]
    
            ]);
    
        }
        catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to reject request.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function acceptInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'invitation_id' => 'required|integer'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ], 422);
    
        }
    
        DB::beginTransaction();
    
        try {
    
            $loginUser = auth()->user();
    
            $invitation = DB::table('group_invitations')
    
                ->where('id', $request->invitation_id)
    
                ->where('invitee_user_id', $loginUser->id)
    
                ->where('status', 'pending')
    
                ->first();
    
            if (!$invitation) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Invitation not found.',
                    'data' => (object)[]
                ], 404);
    
            }
    
            $group = DB::table('society_groups')
    
                ->where('id', $invitation->group_id)
    
                ->whereNull('deleted_at')
    
                ->first();
    
            if (!$group) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Group not found.',
                    'data' => (object)[]
                ], 404);
    
            }
    
            $exists = DB::table('group_members')
    
                ->where('group_id', $group->id)
    
                ->where('user_id', $loginUser->id)
    
                ->where('status', 'approved')
    
                ->exists();
    
            if ($exists) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'You are already a member.',
                    'data' => (object)[]
                ], 422);
    
            }
    
            DB::table('group_members')->insert([
    
                'group_id'   => $group->id,
    
                'user_id'    => $loginUser->id,
    
                'role'       => 'member',
    
                'status'     => 'approved',
    
                'joined_at'  => now(),
    
                'created_at' => now(),
    
                'updated_at' => now()
    
            ]);
    
            DB::table('group_invitations')
    
                ->where('id', $invitation->id)
    
                ->update([
    
                    'status' => 'accepted',
    
                    'accepted_at' => now(),
    
                    'updated_at' => now()
    
                ]);
    
            DB::table('society_groups')
                ->where('id', $group->id)
                ->increment('member_count');
    
    
            DB::table('group_join_requests')->insert([
                'group_id' => $group->id,
                'user_id' => $loginUser->id,
                'requested_by' => 'direct_invite',
                'approved_by' => $invitation->invited_by,
                'approved_at' => now(),
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            DB::commit();
            
            $fcm = $loginUser->fcm_token;
            
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$group->id}";
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebase->manageTopicSubscription('subscribe', $fcm, $topic);
            
            $ow_data = DB::table('customer_register')
                ->where([
                    'id' => $invitation->invited_by, 
                    'deletes' => '0'
                ])
                ->first();
                        
            if ($ow_data) {
                $fcmTokens = $ow_data->fcm_token;
            
                if (!empty($fcmTokens)) {
                    $accessToken = $this->getAccessToken();
            
                    if ($accessToken) {
                  
                        $title = "Invitation Accepted";
                        $body = "{$inviter->name} has accepted your invitation to join {$group->name}.";
            
                        $this->sendFCM(
                            $accessToken,
                            $fcmTokens,
                            $title,
                            $body,
                            [
                                'group_id' => (string) $group->id,
                                'type'     => 'job_invitation_accept',
                                'action'   => 'job_invitation_accept',
                                'screen'   => '/home',
                                'sound'    => 'custom_notification',
                                'invite_token' => ""
                            ]
                        );
                    }
                }
            }
            
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Invitation accepted successfully.',
    
                'data' => (object)[]
    
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to accept invitation.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    
    }
    
    public function rejectInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'invitation_id' => 'required|integer'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ], 422);
    
        }
    
        DB::beginTransaction();
    
        try {
    
            $loginUser = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Invitation
            |--------------------------------------------------------------------------
            */
    
            $invitation = DB::table('group_invitations')
    
                ->where('id', $request->invitation_id)
    
                ->where('invitee_user_id', $loginUser->id)
    
                ->where('status', 'pending')
    
                ->first();
    
            if (!$invitation) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Invitation not found.',
                    'data' => (object)[]
                ], 404);
    
            }
            
            $group = DB::table('society_groups')
    
                ->where('id', $invitation->group_id)
    
                ->whereNull('deleted_at')
    
                ->first();
    
            if (!$group) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Group not found.',
                    'data' => (object)[]
                ], 404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Reject Invitation
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_invitations')
    
                ->where('id', $invitation->id)
    
                ->update([
    
                    'status' => 'rejected',
    
                    'rejected_at' => now(),
    
                    'updated_at' => now()
    
                ]);
    
            DB::commit();
            
            // 1. Fetch the inviter's details to notify them
            $ow_data = DB::table('customer_register')
                ->where([
                    'id' => $invitation->invited_by, 
                    'deletes' => 0 
                ])
                ->first();
                        
            if ($ow_data) {
                $fcmTokens = $ow_data->fcm_token;
            
                if (!empty($fcmTokens)) {
                    $accessToken = $this->getAccessToken();
            
                    if ($accessToken) {
                        // Professional & clean copy for decline event
                        $title = "Invitation Declined";
                        $body = "{$inviter->name} has declined your invitation to join {$group->name}.";
            
                        $this->sendFCM(
                            $accessToken,
                            $fcmTokens, 
                            $title,
                            $body,
                            [
                                'group_id' => (string) $invitation->group_id, 
                                'type'     => 'job_invitation_reject', // Updated event type
                                'action'   => 'job_invitation_reject', // Updated action trigger
                                'screen'   => '/home',
                                'sound'    => 'custom_notification',
                                'invite_token' => ""
                            ]
                        );
                    }
                }
            }
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Invitation rejected successfully.',
    
                'data' => (object)[]
    
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to reject invitation.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    
    }
    
}