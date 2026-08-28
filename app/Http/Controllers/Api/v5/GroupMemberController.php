<?php

namespace App\Http\Controllers\Api\v5;

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
use App\Services\AutomationEventService;
use App\Services\PusherService;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use App\Helpers\userLocationLog;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class GroupMemberController extends Controller
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
        
        if($data['invite_token'] == 'job_invitation'){
            
            $stringData['actions'] = json_encode([
                [
                    'id' => 'accept',
                    'title' => 'Accept'
                ],
                [
                    'id' => 'reject',
                    'title' => 'Not Interested'
                ]
            ]);
            
        }
        
        if($stringData['title'] == '' || $stringData['title'] == null || $stringData['body'] == '' || $stringData['body'] == null){
            return;
        }
        
        $stringData['invite_token'] = $data['invite_token'] ?? '';
    
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
                    ]
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
                            'sound' => 'custom_notification.mp3',
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
    
    public function memberList(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'group_id' => 'required|integer',
    
            'search' => 'nullable|string|max:100',
    
            'page' => 'nullable|integer|min:1',
    
            'per_page' => 'nullable|integer|min:1|max:100'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ],422);
    
        }
    
        try{
    
            $user = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Check Group
            |--------------------------------------------------------------------------
            */
    
            $group = DB::table('society_groups')
    
                ->where('id',$request->group_id)
    
                ->whereNull('deleted_at')
    
                ->first();
    
            if(!$group){
    
                return response()->json([
    
                    'status'=>false,
    
                    'message'=>'Group not found.',
    
                    'data'=>(object)[]
    
                ],404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Check Membership
            |--------------------------------------------------------------------------
            */
    
            $isMember = DB::table('group_members')
    
                ->where('group_id',$group->id)
    
                ->where('user_id',$user->id)
    
                ->where('status','approved')
    
                ->exists();
    
            if(!$isMember){
    
                return response()->json([
    
                    'status'=>false,
    
                    'message'=>'You are not a member of this group.',
    
                    'data'=>(object)[]
    
                ],403);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */
    
            $page = $request->page ?? 1;
    
            $perPage = $request->per_page ?? 20;
    
            $offset = ($page-1) * $perPage;
    
            /*
            |--------------------------------------------------------------------------
            | Query
            |--------------------------------------------------------------------------
            */
    
            $query = DB::table('group_members as gm')
                ->join('customer_register as c', 'c.id', '=', 'gm.user_id')
                ->leftJoin('kyc_carpool as cp', 'c.id', '=', 'cp.user_id')
                ->where('gm.group_id', $group->id)
                ->where('gm.status', 'approved');
            
            if (!empty($request->search)) {
                $query->where(function($q) use ($request) {
                    $q->where('c.name', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('c.mobile', 'LIKE', '%' . $request->search . '%');
                });
            }
            
            $total = (clone $query)->count();
            
            $members = $query
                ->select(
                    'gm.id',
                    'gm.user_id',
                    'gm.role',
                    'gm.status',
                    'gm.joined_at',
                    'c.name',
                    'c.mobile',
                    // COALESCE returns the first non-null value it encounters
                    DB::raw("COALESCE(c.profile_img_url, cp.selfie_url) as profile_img_url")
                )
                ->orderByRaw("
                    CASE 
                        WHEN gm.role = 'owner' THEN 1
                        WHEN gm.role = 'admin' THEN 2
                        ELSE 3
                    END
                ")
                ->orderBy('c.name')
                ->offset($offset)
                ->limit($perPage)
                ->get();
    
            $ownerCount = DB::table('group_members')
    
                ->where('group_id',$group->id)
    
                ->where('role','owner')
    
                ->where('status','approved')
    
                ->count();
    
            $adminCount = DB::table('group_members')
    
                ->where('group_id',$group->id)
    
                ->where('role','admin')
    
                ->where('status','approved')
    
                ->count();
    
            $memberCount = DB::table('group_members')
    
                ->where('group_id',$group->id)
    
                ->where('role','member')
    
                ->where('status','approved')
    
                ->count();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Members fetched successfully.',
    
                'data'=>[
    
                    'statistics'=>[
    
                        'owners'=>$ownerCount,
    
                        'admins'=>$adminCount,
    
                        'members'=>$memberCount,
    
                        'total_members'=>$total
    
                    ],
    
                    'pagination'=>[
    
                        'current_page'=>(int)$page,
    
                        'per_page'=>(int)$perPage,
    
                        'total_records'=>$total,
    
                        'total_pages'=>ceil($total/$perPage)
    
                    ],
    
                    'members'=>$members
    
                ]
    
            ]);
    
        }
        catch(\Exception $e){
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to fetch members.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function inviteMember(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'group_id'=>'required|integer',
            'user_id'=>'nullable|integer',
            'mobile'=>'nullable|string|max:20',
            'message'=>'nullable|string|max:500'
        ]);
    
        if($validator->fails()){
    
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()->first(),
                'data'=>(object)[]
            ],422);
    
        }
    
        if(empty($request->user_id) && empty($request->mobile)){
    
            return response()->json([
                'status'=>false,
                'message'=>'User or Mobile is required.',
                'data'=>(object)[]
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
    
            $inviteUser = null;
    
            if($request->filled('user_id')){
    
                $inviteUser = DB::table('customer_register')
                    ->where('id',$request->user_id)
                    ->where('deletes', '0')
                    ->first();
    
            }
    
            if(!$inviteUser && $request->filled('mobile')){
    
                $inviteUser = DB::table('customer_register')
                    ->where('mobile',$request->mobile)
                    ->where('deletes', '0')
                    ->first();
    
            }
    
            $inviteUserId = $inviteUser->id ?? null;
    
            if($inviteUserId){
    
                $exists = DB::table('group_members')
                    ->where('group_id',$group->id)
                    ->where('user_id',$inviteUserId)
                    ->where('status','approved')
                    ->exists();
    
                if($exists){
    
                    DB::rollBack();
    
                    return response()->json([
                        'status'=>false,
                        'message'=>'User already exists in this group.',
                        'data'=>(object)[]
                    ],422);
    
                }
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Pending Invitation
            |--------------------------------------------------------------------------
            */
    
            $pending = DB::table('group_invitations')
                ->where('group_id',$group->id)
                ->where(function($q) use($inviteUserId,$request){
    
                    if($inviteUserId){
    
                        $q->where('invitee_user_id',$inviteUserId);
    
                    }else{
    
                        $q->where('invitee_mobile',$request->mobile);
    
                    }
    
                })
                ->where('status','pending')
                ->exists();
    
            if($pending){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Invitation already sent.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Create Invitation
            |--------------------------------------------------------------------------
            */
    
            $token = Str::random(40);
    
            $inviteId = DB::table('group_invitations')
                ->insertGetId([
    
                    'group_id'=>$group->id,
    
                    'invited_by'=>$user->id,
    
                    'invite_type'=>'user',
    
                    'invitee_user_id'=>$inviteUserId,
    
                    'invitee_mobile'=>$request->mobile,
    
                    'invite_token'=>$token,
    
                    'message'=>$request->message,
    
                    'expired_at'=>now()->addDays(7),
    
                    'status'=>'pending',
    
                    'created_at'=>now(),
    
                    'updated_at'=>now()
    
                ]);
            
            $fcmTokens = $inviteUser->fcm_token ?? null;
            
            if (!empty($fcmTokens)) {
                
                $accessToken = $this->getAccessToken();
            
                if ($accessToken) {
            
                    $title = "👥 New Group Invitation";
                    $body = "{$user->name} invited you to join the group \"{$group->name}\"";
            
                    $this->sendFCM(
                        $accessToken,
                        $fcmTokens,
                        $title,
                        $body,
                        [
                            'group_id'     => (string)$group->id,
                            'type'         => 'group_invitation',
                            'action'       => 'group_invitation',
                            'screen'       => '/group-details',
                            'sound'        => 'custom_notification',
                            'invite_token'  => $group->invite_code
                        ]
                    );
                }
            }
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Invitation sent successfully.',
    
                'data'=>[
    
                    'invitation_id'=>$inviteId,
    
                    'invite_token'=>$token,
    
                    'invite_link'=>url('/group/invite/'.$token)
    
                ]
    
            ]);
    
        }catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>$e->getMessage(),
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function removeMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'group_id' => 'required|integer',
    
            'member_id' => 'required|integer'
    
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
            | Group
            |--------------------------------------------------------------------------
            */
    
            $group = DB::table('society_groups')
                ->where('id', $request->group_id)
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
            | Login User Role
            |--------------------------------------------------------------------------
            */
    
            $myRole = DB::table('group_members')
                ->where('group_id', $group->id)
                ->where('user_id', $loginUser->id)
                ->where('status', 'approved')
                ->first();
    
            if (!$myRole) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Permission denied.',
                    'data' => (object)[]
                ], 403);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Member Details
            |--------------------------------------------------------------------------
            */
    
            $member = DB::table('group_members')
                ->where('group_id', $group->id)
                ->where('user_id', $request->member_id)
                ->where('status', 'approved')
                ->first();
    
            if (!$member) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Member not found.',
                    'data' => (object)[]
                ], 404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Self Remove
            |--------------------------------------------------------------------------
            */
    
            if ($member->user_id == $loginUser->id) {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Use Leave Group API.',
                    'data' => (object)[]
                ], 422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Owner Cannot Remove Owner
            |--------------------------------------------------------------------------
            */
    
            if ($member->role == 'owner') {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Owner cannot be removed.',
                    'data' => (object)[]
                ], 422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Admin Rules
            |--------------------------------------------------------------------------
            */
    
            if ($myRole->role == 'member') {
    
                DB::rollBack();
    
                return response()->json([
                    'status' => false,
                    'message' => 'Permission denied.',
                    'data' => (object)[]
                ], 403);
    
            }
    
            if ($myRole->role == 'admin') {
    
                if ($member->role != 'member') {
    
                    DB::rollBack();
    
                    return response()->json([
                        'status' => false,
                        'message' => 'Admin can remove only members.',
                        'data' => (object)[]
                    ], 403);
    
                }
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Remove Member
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_members')
                ->where('id', $member->id)
                ->update([
    
                    'status' => 'removed',
    
                    'removed_by' => $loginUser->id,
    
                    'removed_at' => now(),
    
                    'updated_at' => now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | Update Counter
            |--------------------------------------------------------------------------
            */
    
            DB::table('society_groups')
                ->where('id', $group->id)
                ->decrement('member_count');
    
            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            */
    
            /*
                Remove Socket Room
    
                Push Notification
    
                Chat Permission
    
                Cancel Pending Requests
            */
    
            DB::commit();
            
            
            $fcm = DB::table('customer_register')->where('id', $member->id)->value('fcm_token')??null;
            
            $loginUser->fcm_token;
            
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$group->id}";
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
            if($fcm){
                $firebase->manageTopicSubscription('unsubscribe', $fcm, $topic);
            }
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Member removed successfully.',
    
                'data' => (object)[]
    
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to remove member.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    
    }
    
    public function leaveGroup(Request $request)
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
    
            $loginUser = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Group
            |--------------------------------------------------------------------------
            */
    
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
    
            /*
            |--------------------------------------------------------------------------
            | Member
            |--------------------------------------------------------------------------
            */
    
            $member = DB::table('group_members')
    
                ->where('group_id',$group->id)
    
                ->where('user_id',$loginUser->id)
    
                ->where('status','approved')
    
                ->first();
    
            if(!$member){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'You are not a member of this group.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Owner Cannot Leave
            |--------------------------------------------------------------------------
            */
    
            if($member->role == 'owner'){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Transfer ownership before leaving the group.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Leave Group
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_members')
    
                ->where('id',$member->id)
    
                ->update([
    
                    'status'=>'left',
    
                    'removed_by'=>$loginUser->id,
    
                    'removed_at'=>now(),
    
                    'updated_at'=>now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | Update Member Count
            |--------------------------------------------------------------------------
            */
    
            DB::table('society_groups')
    
                ->where('id',$group->id)
    
                ->decrement('member_count');
    
            /*
            |--------------------------------------------------------------------------
            | Cancel Pending Invitations
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_invitations')
    
                ->where('group_id',$group->id)
    
                ->where('invitee_user_id',$loginUser->id)
    
                ->where('status','pending')
    
                ->update([
    
                    'status'=>'expired',
    
                    'updated_at'=>now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            */
    
            /*
                Push Notification
    
                Socket Leave Room
    
                Chat Permission Remove
    
                Activity Log
    
                Timeline Log
    
            */
    
            DB::commit();
            
            $fcm = $loginUser->fcm_token;
            
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$group->id}";
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebase->manageTopicSubscription('unsubscribe', $fcm, $topic);
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'You have left the group successfully.',
    
                'data'=>(object)[]
    
            ]);
    
        }
        catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to leave group.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function transferOwnership(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'group_id'=>'required|integer',
            'member_id'=>'required|integer'
        ]);
    
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()->first(),
                'data'=>(object)[]
            ],422);
        }
    
        DB::beginTransaction();
    
        try{
    
            $loginUser = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Owner
            |--------------------------------------------------------------------------
            */
    
            $owner = DB::table('group_members')
    
                ->where('group_id',$request->group_id)
    
                ->where('user_id',$loginUser->id)
    
                ->where('role','owner')
    
                ->where('status','approved')
    
                ->first();
    
            if(!$owner){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Only owner can transfer ownership.',
                    'data'=>(object)[]
                ],403);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | New Owner
            |--------------------------------------------------------------------------
            */
    
            $member = DB::table('group_members')
    
                ->where('group_id',$request->group_id)
    
                ->where('user_id',$request->member_id)
    
                ->where('status','approved')
    
                ->first();
                
            
    
            if(!$member){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=> $request->member_id,
                    'data'=>(object)[]
                ],404);
    
            }
    
            if($member->role=='owner'){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Already owner.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Update Roles
            |--------------------------------------------------------------------------
            */
    
            DB::table('group_members')
    
                ->where('id',$owner->id)
    
                ->update([
    
                    'role'=>'admin',
    
                    'updated_at'=>now()
    
                ]);
    
            DB::table('group_members')
                ->where('id',$member->id)
                ->update([
                    'role'=>'owner',
    
                    'updated_at'=>now()
    
                ]);
    
            /*
            |--------------------------------------------------------------------------
            | Update Group
            |--------------------------------------------------------------------------
            */
    
            DB::table('society_groups')
    
                ->where('id',$request->group_id)
    
                ->update([
    
                    'created_by'=>$member->user_id,
    
                    'updated_at'=>now()
    
                ]);
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Ownership transferred successfully.',
    
                'data'=>(object)[]
    
            ]);
    
        }catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
                'status'=>false,
                'message'=>'Unable to transfer ownership.',
                'data'=>(object)[]
            ],500);
    
        }
    
    }
    
    public function makeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'group_id'=>'required|integer',
            'member_id'=>'required|integer'
        ]);
    
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()->first(),
                'data'=>(object)[]
            ],422);
        }
    
        DB::beginTransaction();
    
        try{
    
            $loginUser = auth()->user();
    
            $owner = DB::table('group_members')
    
                ->where('group_id',$request->group_id)
    
                ->where('user_id',$loginUser->id)
    
                ->where('role','owner')
    
                ->where('status','approved')
    
                ->exists();
    
            if(!$owner){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Only owner can assign admin.',
                    'data'=>(object)[]
                ],403);
    
            }
    
            $member = DB::table('group_members')
    
                ->where('group_id',$request->group_id)
    
                ->where('user_id',$request->member_id)
    
                ->where('status','approved')
    
                ->first();
    
            if(!$member){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Member not found.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            if($member->role!='member'){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'User is already admin/owner.',
                    'data'=>(object)[]
                ],422);
    
            }
    
            DB::table('group_members')
    
                ->where('id',$member->id)
    
                ->update([
    
                    'role'=>'admin',
    
                    'updated_at'=>now()
    
                ]);
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Admin assigned successfully.',
    
                'data'=>(object)[]
    
            ]);
    
        }catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
                'status'=>false,
                'message'=>'Unable to assign admin.',
                'data'=>(object)[]
            ],500);
    
        }
    
    }
    
    public function removeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'group_id'=>'required|integer',
            'member_id'=>'required|integer'
        ]);
    
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()->first(),
                'data'=>(object)[]
            ],422);
        }
    
        DB::beginTransaction();
    
        try{
    
            $loginUser = auth()->user();
    
            $owner = DB::table('group_members')
    
                ->where('group_id',$request->group_id)
    
                ->where('user_id',$loginUser->id)
    
                ->where('role','owner')
    
                ->where('status','approved')
    
                ->exists();
    
            if(!$owner){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Only owner can remove admin.',
                    'data'=>(object)[]
                ],403);
    
            }
    
            $admin = DB::table('group_members')
    
                ->where('group_id',$request->group_id)
    
                ->where('user_id',$request->member_id)
    
                ->where('role','admin')
    
                ->where('status','approved')
    
                ->first();
    
            if(!$admin){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Admin not found.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            DB::table('group_members')
    
                ->where('id',$admin->id)
    
                ->update([
    
                    'role'=>'member',
    
                    'updated_at'=>now()
    
                ]);
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Admin removed successfully.',
    
                'data'=>(object)[]
    
            ]);
    
        }catch(\Exception $e){
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
                'status'=>false,
                'message'=>'Unable to remove admin.',
                'data'=>(object)[]
            ],500);
    
        }
    
    }
    
}