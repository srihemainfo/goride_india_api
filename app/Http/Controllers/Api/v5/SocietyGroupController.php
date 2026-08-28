<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SocietyGroupController extends Controller
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
    
    public function categoryList(Request $request)
    {
        try {
            $user = auth()->user();
    
            $cat = DB::table('group_categories as gc')
                ->select('gc.id', 'gc.name', 'gc.icon', 'gc.status')
                ->where('status', 1)
                ->whereNull('gc.deleted_at')
                ->get();
    
            if ($cat->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Group categories not found.',
                    'data'    => []
                ], 404);
            }
    
            return response()->json([
                'status'  => true,
                'message' => 'Group categories fetched successfully.',
                'data'    => $cat
            ]);
    
        } catch (\Exception $e) {
            \Log::error($e);
    
            return response()->json([
                'status'  => false,
                'message' => 'Unable to fetch group details.',
                'data'    => []
            ], 500);
        }
    }
    
    public function createGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'privacy_type' => 'required|in:private,public',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ], 422);
        }
        
        $groupName = trim($request->name);
        
        $groupExists = DB::table('society_groups')
            ->where('category_id', $request->category_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($groupName)])
            ->where('privacy_type', $request->privacy_type)
            ->whereNull('deleted_at')
            ->exists();
    
        if ($groupExists) {
            return response()->json([
                'status' => false,
                'message' => 'A group with this name already exists in this category.',
                'data' => (object)[]
            ], 409);
        }
    
        DB::beginTransaction();
    
        try {
            $user = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Upload Image
            |--------------------------------------------------------------------------
            */
            $image = null;
    
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($request->name));
                
                $fileName = uniqid($safeName . '_') . '.' . $file->getClientOriginalExtension();
                
                $path = $file->storeAs(
                    'society/groups',
                    $fileName,
                    's3'
                );
    
                // Set visibility and construct URL if path generation succeeds
                if (!empty($path)) {
                    Storage::disk('s3')->setVisibility($path, 'public');
                    $image = Storage::disk('s3')->url($path);
                }
            }
    
            /*
            |--------------------------------------------------------------------------
            | Generate Invite Code
            |--------------------------------------------------------------------------
            */
            do {
                $inviteCode = strtoupper(Str::random(8));
    
                $exists = DB::table('society_groups')
                    ->where('invite_code', $inviteCode)
                    ->exists();
    
            } while ($exists);
    
            $groupStatus = $request->privacy_type == 'public'
                ? 'pending'
                : 'approved';
            
            $joinType = $request->privacy_type == 'public'
                ? 'auto'
                : 'approval';
            
            $groupId = DB::table('society_groups')->insertGetId([
                'group_uuid'     => Str::uuid(),
                'created_by'     => $user->id,
                'category_id'    => $request->category_id,
                'name'           => $groupName,
                'description'    => $request->description,
                'image'          => $image,
                'privacy_type'   => $request->privacy_type,
                'join_type'      => $joinType,
                // 'approval_type'  => 'manual',
                'status'         => $groupStatus,
                'member_count'   => 1,
                'job_count'      => 0,
                'invite_code'    => $inviteCode,
                'invite_link'    => url('/group/join/'.$inviteCode),
                'created_at'     => now(),
                'updated_at'     => now()
            ]);
            
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$groupId}";
    
            /*
            |--------------------------------------------------------------------------
            | Add Owner
            |--------------------------------------------------------------------------
            */
            DB::table('group_members')->insert([
                'group_id'   => $groupId,
                'user_id'    => $user->id,
                'role'       => 'owner',
                'status'     => 'approved',
                'joined_at'  => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            /*
            |--------------------------------------------------------------------------
            | Get Group
            |--------------------------------------------------------------------------
            */
            $group = DB::table('society_groups')
                ->leftJoin(
                    'group_categories',
                    'group_categories.id',
                    '=',
                    'society_groups.category_id'
                )
                ->select(
                    'society_groups.*',
                    'group_categories.name as category_name'
                )
                ->where('society_groups.id', $groupId)
                ->first();
    
            DB::commit();
            
            $fcm = $user->fcm_token;
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebase->manageTopicSubscription('subscribe', $fcm, $topic);
    
            return response()->json([
                'status' => true,
                'message' => 'Group created successfully.',
                'data' => $group,
                'topic' => $topic
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }
    
    public function updateGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id'      => 'required|integer',
            'name'          => 'required|string|max:150',
            'description'   => 'nullable|string|max:1000',
            'category_id'   => 'required|exists:group_categories,id',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // 'privacy_type'  => 'required|in:private,public'
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
            $user = auth()->user();
    
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
    
            $permission = DB::table('group_members')
                ->where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->whereIn('role', ['owner', 'admin'])
                ->where('status', 'approved')
                ->exists();
    
            if (!$permission) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to update this group.',
                    'data' => (object)[]
                ], 403);
            }
    
            $duplicate = DB::table('society_groups')
                ->where('id', '!=', $group->id)
                ->whereRaw('LOWER(name)=?', [strtolower(trim($request->name))])
                ->where('category_id', $request->category_id)
                ->where('privacy_type', $group->privacy_type)
                ->whereNull('deleted_at')
                ->exists();
    
            if ($duplicate) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Group name already exists.',
                    'data' => (object)[]
                ], 422);
            }
            
            $updateData = [
                'name'        => trim($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'updated_at'  => now()
            ];
    
            /*
            |--------------------------------------------------------------------------
            | Handle Image Replacement (Delete Old & Upload New)
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                
                
                if (!empty($group->image)) {
                    
                    $oldPath = parse_url($group->image, PHP_URL_PATH);
                    $oldPath = ltrim($oldPath, '/'); 
                    
                    Storage::disk('s3')->delete($oldPath);
                }
                
                $file = $request->file('image');
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($request->name));
                $fileName = uniqid($safeName . '_') . '.' . $file->getClientOriginalExtension();
    
                $path = $file->storeAs(
                    'society/groups',
                    $fileName,
                    's3'
                );
    
                if (!empty($path)) {
                    Storage::disk('s3')->setVisibility($path, 'public');
                    $updateData['image'] = Storage::disk('s3')->url($path);
                }
            }
    
            DB::table('society_groups')
                ->where('id', $group->id)
                ->update($updateData);
    
            $updatedGroup = DB::table('society_groups')
                ->leftJoin(
                    'group_categories',
                    'group_categories.id',
                    '=',
                    'society_groups.category_id'
                )
                ->select(
                    'society_groups.*',
                    'group_categories.name as category_name'
                )
                ->where('society_groups.id', $group->id)
                ->first();
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Group updated successfully.',
                'data' => $updatedGroup
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Edit Group Error', [
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine()
    
            ]);
    
            return response()->json([
                'status' => false,
                'message' => 'Unable to update group.',
                'data' => (object)[]
            ], 500);
        }
    }
    
    public function deleteGroup(Request $request)
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
    
        try {
    
            $user = auth()->user();
    
            $group = DB::table('society_groups')
                ->where('id',$request->group_id)
                ->whereNull('deleted_at')
                ->first();
                
            $topic = "group_job_notify_" . env('APP_ENV') . "_{$group->id}";
    
            if(!$group){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Group not found.',
                    'data'=>(object)[]
                ],404);
    
            }
    
            $owner = DB::table('group_members')
                ->where('group_id',$group->id)
                ->where('user_id',$user->id)
                ->where('role','owner')
                ->where('status','approved')
                ->exists();
    
            if(!$owner){
    
                DB::rollBack();
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Only owner can delete this group.',
                    'data'=>(object)[]
                ],403);
    
            }
            
            // $topic = "group_job_notify_" . env('APP_ENV') . "_{$group->id}";
    
            DB::table('society_groups')
                ->where('id',$group->id)
                ->update([
    
                    'deleted_at'=>now(),
    
                    'updated_at'=>now()
    
                ]);
    
            DB::table('group_join_requests')
                ->where('group_id',$group->id)
                ->where('status','pending')
                ->delete();
    
            DB::table('group_invitations')
                ->where('group_id',$group->id)
                ->where('status','pending')
                ->update([
    
                    'status'=>'expired',
    
                    'updated_at'=>now()
    
                ]);
    
            DB::table('group_members')
                ->where('group_id',$group->id)
                ->update([
    
                    'status'=>'removed',
    
                    'updated_at'=>now()
    
                ]);
                
            
    
            DB::commit();
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Group deleted successfully.',
    
                'data'=>(object)[],
                'topic' => $topic
    
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to delete group.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    }
    
    // public function groupDetails(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'group_id' => 'required|integer'
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first(),
    //             'data' => (object)[]
    //         ],422);
    //     }
    
    //     try {
    
    //         $user = auth()->user();
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Check Group
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $group = DB::table('society_groups as sg')
    
    //             ->leftJoin('group_categories as gc','gc.id','=','sg.category_id')
    
    //             ->leftJoin('customer_register as cr','cr.id','=','sg.created_by')
    
    //             ->select(
    
    //                 'sg.id',
    //                 'sg.group_uuid',
    //                 'sg.name',
    //                 'sg.description',
    //                 'sg.image',
    //                 'sg.privacy_type',
    //                 'sg.status',
    //                 'sg.member_count',
    //                 'sg.job_count',
    //                 'sg.invite_code',
    //                 'sg.invite_link',
    //                 'sg.created_at',
    
    //                 'gc.name as category_name',
    
    //                 'cr.id as owner_id',
    //                 'cr.name as owner_name',
    //                 'cr.profile_img_url as owner_image'
    
    //             )
    
    //             ->where('sg.id',$request->group_id)
    
    //             ->whereNull('sg.deleted_at')
    
    //             ->first();
    
    //         if(!$group){
    
    //             return response()->json([
    //                 'status'=>false,
    //                 'message'=>'Group not found.',
    //                 'data'=>(object)[]
    //             ],404);
    
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Check Membership
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $member = DB::table('group_members')
    
    //             ->where('group_id',$group->id)
    
    //             ->where('user_id',$user->id)
    
    //             ->where('status','approved')
    
    //             ->first();
    
    //         if(!$member){
    
    //             return response()->json([
    //                 'status'=>false,
    //                 'message'=>'You are not a member of this group.',
    //                 'data'=>(object)[]
    //             ],403);
    
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Member Count
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $memberCount = DB::table('group_members')
    
    //             ->where('group_id',$group->id)
    
    //             ->where('status','approved')
    
    //             ->count();
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Pending Join Requests
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $pendingRequests = DB::table('group_join_requests')
    
    //             ->where('group_id',$group->id)
    
    //             ->where('status','pending')
    
    //             ->count();
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Logged User Permission
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $canEdit = false;
    //         $canDelete = false;
    //         $canInvite = false;
    
    //         if(in_array($member->role,['owner','admin'])){
    
    //             $canEdit = true;
    //             $canInvite = true;
    
    //         }
    
    //         if($member->role == 'owner'){
    
    //             $canDelete = true;
    
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Response
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $data = [
    
    //             'group' => $group,
    
    //             'logged_user'=>[
    
    //                 'id'=>$user->id,
    //                 'role'=>$member->role,
    //                 'status'=>$member->status
    
    //             ],
    
    //             'statistics'=>[
    
    //                 'member_count'=>$memberCount,
    //                 'job_count'=>$group->job_count,
    //                 'pending_requests'=>$pendingRequests
    
    //             ],
    
    //             'permissions'=>[
    
    //                 'can_edit'=>$canEdit,
    //                 'can_delete'=>$canDelete,
    //                 'can_invite'=>$canInvite
    
    //             ]
    
    //         ];
    
    //         return response()->json([
    
    //             'status'=>true,
    
    //             'message'=>'Group details fetched successfully.',
    
    //             'data'=>$data
    
    //         ]);
    
    //     }
    //     catch(\Exception $e){
    
    //         Log::error($e);
    
    //         return response()->json([
    
    //             'status'=>false,
    
    //             'message'=>$e->getMessage(),
    
    //             'data'=>(object)[]
    
    //         ],500);
    
    //     }
    
    // }
    
    public function groupDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'group_id' => 'required|integer'
    
        ]);
    
        if ($validator->fails()) {
    
            return response()->json([
    
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
    
            ], 422);
    
        }
    
        try {
    
            $userId = auth()->id();
    
            /*
            |--------------------------------------------------------------------------
            | Group Details
            |--------------------------------------------------------------------------
            */
    
            $group = DB::table('society_groups as sg')
    
                ->leftJoin('group_categories as gc', 'gc.id', '=', 'sg.category_id')
    
                // ->leftJoin('cities as city', 'city.id', '=', 'sg.city_id')
    
                ->where('sg.id', $request->group_id)
    
                ->whereNull('sg.deleted_at')
    
                ->select(
    
                    'sg.id',
                    'sg.group_uuid',
                    'sg.name',
                    'sg.description',
                    'sg.image',
                    'sg.privacy_type',
                    'sg.join_type',
                    'sg.member_count',
                    'sg.job_count',
                    'sg.status',
                    'sg.created_by',
                    'sg.created_at',
    
                    'gc.id as category_id',
                    'gc.name as category_name',
    
                    // 'city.id as city_id',
                    // 'city.name as city_name'
    
                )
    
                ->first();
    
            if (!$group) {
    
                return response()->json([
    
                    'status' => false,
                    'message' => 'Group not found.',
                    'data' => (object)[]
    
                ], 404);
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Group Image
            |--------------------------------------------------------------------------
            */
    
            // $group->image = !empty($group->image)
            //     ? Storage::disk('s3')->url($group->image)
            //     : null;
                
            /*
            |--------------------------------------------------------------------------
            | Owner Details
            |--------------------------------------------------------------------------
            */
    
            $owner = DB::table('group_members as gm')
    
                ->join('customer_register as c', 'c.id', '=', 'gm.user_id')
    
                ->where('gm.group_id', $group->id)
    
                ->where('gm.role', 'owner')
    
                ->where('gm.status', 'approved')
    
                ->select(
    
                    'c.id',
    
                    'c.name',
    
                    'c.profile_img_url',
    
                    'gm.joined_at'
    
                )
    
                ->first();
    
            if ($owner) {
    
                // $owner->profile_img_url = !empty($owner->profile_img_url)
                //     ? Storage::disk('s3')->url($owner->profile_img_url)
                //     : null;
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Admin List
            |--------------------------------------------------------------------------
            */
    
            $admins = DB::table('group_members as gm')
    
                ->join('customer_register as c', 'c.id', '=', 'gm.user_id')
    
                ->where('gm.group_id', $group->id)
    
                ->whereIn('gm.role', ['owner', 'admin'])
    
                ->where('gm.status', 'approved')
    
                ->select(
    
                    'c.id',
    
                    'c.name',
    
                    'c.profile_img_url',
    
                    'gm.role'
    
                )
    
                ->get();
    
            foreach ($admins as $admin) {
    
                // $admin->profile_img_url = !empty($admin->profile_img_url)
                //     ? Storage::disk('s3')->url($admin->profile_img_url)
                //     : null;
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Latest Members
            |--------------------------------------------------------------------------
            */
    
            $members = DB::table('group_members as gm')
    
                ->join('customer_register as c', 'c.id', '=', 'gm.user_id')
    
                ->where('gm.group_id', $group->id)
    
                ->where('gm.status', 'approved')
    
                ->orderByDesc('gm.id')
    
                ->limit(10)
    
                ->select(
    
                    'c.id',
    
                    'c.name',
    
                    'c.profile_img_url'
    
                )
    
                ->get();
    
            foreach ($members as $member) {
    
                // $member->profile_img_url = !empty($member->profile_img_url)
                //     ? Storage::disk('s3')->url($member->profile_img_url)
                //     : null;
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Logged User Status
            |--------------------------------------------------------------------------
            */
    
            $member = DB::table('group_members')
    
                ->where('group_id', $group->id)
    
                ->where('user_id', $userId)
    
                ->where('status', 'approved')
    
                ->first();
    
            $pendingRequest = DB::table('group_join_requests')
    
                ->where('group_id', $group->id)
    
                ->where('user_id', $userId)
    
                ->where('status', 'pending')
    
                ->exists();
    
            $group->is_member = !empty($member);
    
            $group->member_role = $member->role ?? null;
    
            $group->requested = $pendingRequest;
    
            $group->can_join = (!$group->is_member && !$pendingRequest);
    
            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */
    
            $statistics = [
    
                'members' => (int) $group->member_count,
    
                'jobs' => (int) $group->job_count,
    
                'admins' => count($admins)
    
            ];
            
            /*
            |--------------------------------------------------------------------------
            | Recent Jobs
            |--------------------------------------------------------------------------
            */
            
            // dd('hi');
    
            $recentJobs = DB::table('group_job_shares as gjs')
                ->join('cus_job_temp as job', 'job.id', '=', 'gjs.job_id')
                ->join('customer_register as c', 'c.id', '=', 'job.user_id')
                ->where('gjs.group_id', $group->id)
                // ->whereNot('job.user_id', $userId)
                ->where('gjs.status', 'active')
                ->orderByDesc('gjs.id')
                ->limit(10)
                ->select(
    
                    'job.id as job_id',
    
                    'job.global_type',
    
                    'job.from_place',
    
                    'job.to_place',
    
                    'job.pickup_date',
    
                    // 'job.pickup_time',
    
                    // 'job.status',
    
                    'c.id as customer_id',
    
                    'c.name as customer_name',
    
                    'c.profile_img_url',
    
                    'gjs.created_at as shared_at'
    
                )
                ->get();
    
            foreach ($recentJobs as $job) {
    
                // $job->profile_img_url = !empty($job->profile_img_url)
                //     ? Storage::disk('s3')->url($job->profile_img_url)
                //     : null;
    
            }
            
            $permissions = [

                'is_owner' => false,
            
                'is_admin' => false,
            
                'can_edit_group' => false,
            
                'can_delete_group' => false,
            
                'can_update_image' => false,
            
                'can_update_description' => false,
            
                'can_invite_members' => false,
            
                'can_remove_members' => false,
            
                'can_approve_join_requests' => false,
            
                'can_manage_admins' => false,
            
                'can_transfer_ownership' => false,
            
                'can_leave_group' => false,
            
                'can_join_group' => false
            
            ];
            
            if ($member) {
            
                $permissions['can_leave_group'] = true;
            
                if ($member->role == 'owner') {
            
                    $permissions['is_owner'] = true;
                    $permissions['is_admin'] = true;
            
                    $permissions['can_edit_group'] = true;
                    $permissions['can_delete_group'] = true;
                    $permissions['can_update_image'] = true;
                    $permissions['can_update_description'] = true;
                    $permissions['can_invite_members'] = $group->status != 'approved' ? false : true;
                    $permissions['can_remove_members'] = true;
                    $permissions['can_approve_join_requests'] = true;
                    $permissions['can_manage_admins'] = true;
                    $permissions['can_transfer_ownership'] = true;
            
                } elseif ($member->role == 'admin') {
            
                    $permissions['is_admin'] = true;
            
                    $permissions['can_invite_members'] = true;
                    $permissions['can_remove_members'] = $group->status != 'approved' ? false : true;
                    $permissions['can_approve_join_requests'] = true;
            
                }
            
            } else {
            
                $permissions['can_join_group'] = !$pendingRequest;
            
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Group details fetched successfully.',
                'data' => [
                    'group' => $group,
                    'owner' => $owner,
                    'admins' => $admins,
                    'latest_members' => $members,
                    'statistics' => $statistics,
                    'recent_jobs' => $recentJobs,
                    'permissions' => $permissions
                ]
    
            ]);
    
        } catch (\Exception $e) {
    
            Log::error('Society Group Details', [
    
                'user_id' => auth()->id(),
    
                'group_id' => $request->group_id,
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine(),
    
                'file' => $e->getFile()
    
            ]);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to fetch group details.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    
    }
    
    public function myGroups(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search'    => 'nullable|string|max:100',
            'page'      => 'nullable|integer|min:1',
            'per_page'  => 'nullable|integer|min:1|max:50'
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
    
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 20;
    
            $offset = ($page - 1) * $perPage;
    
            $query = DB::table('group_members as gm')
    
                ->join('society_groups as sg', 'sg.id', '=', 'gm.group_id')
    
                ->leftJoin('group_categories as gc', 'gc.id', '=', 'sg.category_id')
    
                ->where('gm.user_id', $user->id)
    
                ->where('gm.status', 'approved')
    
                ->whereNull('sg.deleted_at');
    
            if (!empty($request->search)) {
    
                $query->where('sg.name', 'LIKE', '%' . trim($request->search) . '%');
    
            }
    
            $total = (clone $query)->count();
    
            $groups = $query
    
                ->select(
    
                    'sg.id',
                    'sg.group_uuid',
                    'sg.name',
                    'sg.description',
                    'sg.image',
                    'sg.privacy_type',
                    'sg.member_count',
                    'sg.job_count',
                    'sg.created_at',
                    'sg.status',
    
                    'gc.name as category_name',
    
                    'gm.role'
    
                )
    
                ->orderBy('sg.updated_at', 'DESC')
    
                ->offset($offset)
    
                ->limit($perPage)
    
                ->get();
    
            foreach ($groups as $group) {
    
                /*
                |--------------------------------------------------------------------------
                | Latest Members
                |--------------------------------------------------------------------------
                */
    
                $group->recent_members = DB::table('group_members as gm')
    
                    ->join('customer_register as c', 'c.id', '=', 'gm.user_id')
    
                    ->where('gm.group_id', $group->id)
    
                    ->where('gm.status', 'approved')
    
                    ->orderBy('gm.joined_at', 'DESC')
    
                    ->limit(3)
    
                    ->get([
                        'c.id',
                        'c.name',
                        'c.profile_img_url'
                    ]);
    
                /*
                |--------------------------------------------------------------------------
                | Pending Requests
                |--------------------------------------------------------------------------
                */
    
                $group->pending_requests = DB::table('group_join_requests')
    
                    ->where('group_id', $group->id)
    
                    ->where('status', 'pending')
    
                    ->count();
    
                /*
                |--------------------------------------------------------------------------
                | Permissions
                |--------------------------------------------------------------------------
                */
    
                $group->can_edit = in_array($group->role, ['owner', 'admin']);
    
                $group->can_invite = $group->status != 'approved' ? 'false' : in_array($group->role, ['owner', 'admin']);
    
                $group->can_delete = ($group->role == 'owner');
    
            }
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Groups fetched successfully.',
    
                'data' => [
    
                    'current_page' => (int)$page,
    
                    'per_page' => (int)$perPage,
    
                    'total_records' => $total,
    
                    'total_pages' => ceil($total / $perPage),
    
                    'groups' => $groups
    
                ]
    
            ]);
    
        } catch (\Exception $e) {
    
            Log::error($e);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to fetch groups.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    }
    
    public function groups(Request $request)
    {
        $validator = Validator::make($request->all(), [
    
            'page' => 'nullable|integer|min:1',
    
            'per_page' => 'nullable|integer|min:1|max:100',
    
            'search' => 'nullable|string|max:100',
    
            'category_id' => 'nullable|integer',
    
            // 'city_id' => 'nullable|integer'
    
        ]);
        
        // dd('hi');
    
        if ($validator->fails()) {
    
            return response()->json([
    
                'status' => false,
    
                'message' => $validator->errors()->first(),
    
                'data' => (object)[]
    
            ],422);
    
        }
    
        try{
    
            $userId = auth()->id();
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 20;
            $offset = ($page - 1) * $perPage;
    
            $query = DB::table('society_groups AS sg')
                ->leftJoin('group_categories AS gc','gc.id','=','sg.category_id')
                ->leftJoin('cities AS city','city.id','=','sg.city_id')
                ->where('sg.privacy_type','public')
                ->where('sg.status','approved')
                ->whereNull('sg.deleted_at');
    
            if($request->filled('search')){
    
                $search = trim($request->search);
    
                $query->where(function($q) use($search){
    
                    $q->where('sg.name','LIKE',"%{$search}%")
    
                    ->orWhere('sg.description','LIKE',"%{$search}%");
    
                });
    
            }
    
            if($request->filled('category_id')){
    
                $query->where('sg.category_id',$request->category_id);
    
            }
    
    
            $total = (clone $query)->count();
    
            $groups = $query
    
                ->select(
    
                    'sg.id',
    
                    'sg.group_uuid',
    
                    'sg.name',
    
                    'sg.description',
    
                    'sg.image',
    
                    'sg.member_count',
    
                    'sg.job_count',
    
                    'sg.join_type',
    
                    'gc.name AS category_name',
    
                    'city.name AS city_name'
    
                )
    
                ->orderBy('sg.member_count','DESC')
    
                ->offset($offset)
    
                ->limit($perPage)
    
                ->get();
    
            foreach($groups as $group){
    
                // $group->image = !empty($group->image)
                //     ? Storage::disk('s3')->url($group->image)
                //     : null;
    
    
                $member = DB::table('group_members')
                    ->where('group_id',$group->id)
                    ->where('user_id',$userId)
                    ->where('status','approved')
                    ->exists();
    
    
                $requested = DB::table('group_join_requests')
                    ->where('group_id',$group->id)
                    ->where('user_id',$userId)
                    ->where('status','pending')
                    ->exists();
    
                $group->is_member = $member;
                $group->requested = $requested;
                $group->can_join = (!$member && !$requested);
    
            }
    
            return response()->json([
    
                'status'=>true,
    
                'message'=>'Groups fetched successfully.',
    
                'data'=>[
    
                    'pagination'=>[
    
                        'current_page'=>$page,
    
                        'per_page'=>$perPage,
    
                        'total_records'=>$total,
    
                        'total_pages'=>ceil($total/$perPage)
    
                    ],
    
                    'groups'=>$groups
    
                ]
    
            ]);
    
        }catch(\Exception $e){
    
            Log::error($e);
    
            return response()->json([
    
                'status'=>false,
    
                'message'=>'Unable to fetch groups.',
    
                'data'=>(object)[]
    
            ],500);
    
        }
    
    }
    
    public function joinGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer'
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
    
            $userId = auth()->id();
    
            $group = DB::table('society_groups')
                ->where('id', $request->group_id)
                ->where('privacy_type', 'public')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->first();
    
            if (!$group) {
    
                return response()->json([
    
                    'status' => false,
    
                    'message' => 'Group not found.',
    
                    'data' => (object)[]
    
                ], 404);
    
            }
    
            $alreadyMember = DB::table('group_members')
                ->where('group_id', $group->id)
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->exists();
    
            if ($alreadyMember) {
    
                return response()->json([
    
                    'status' => false,
    
                    'message' => 'You are already a member of this group.',
    
                    'data' => (object)[]
    
                ], 422);
    
            }
    
            DB::table('group_members')
                ->insert([
                    'group_id' => $group->id,
                    'user_id' => $userId,
                    'role' => 'member',
                    'status' => 'approved',
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
            DB::table('society_groups')
                ->where('id', $group->id)
                ->increment('member_count');
    
            DB::commit();
    
            $appEnv = env('APP_ENV');
            $topicName = "group_job_notify_{$appEnv}_{$group->id}";
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebase->manageTopicSubscription('subscribe', auth()->user()->fcm_token, $topicName);
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Joined successfully.',
    
                'data' => [
    
                    'group_id' => $group->id,
    
                    'member_status' => 'approved'
    
                ]
    
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            Log::error('Join Group Error', [
    
                'user_id' => auth()->id(),
    
                'group_id' => $request->group_id,
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine()
    
            ]);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Unable to join group.',
    
                'data' => (object)[]
    
            ], 500);
    
        }
    
    }
    
}