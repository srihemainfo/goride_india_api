<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Agora\RtcTokenBuilder;
use App\Services\Agora\AccessToken2;

class AgoraController extends Controller
{
    
    public function refreshToken(Request $request)
    {
        return $this->generateToken($request);
    }
    
    public function generateTokenOld(Request $request)
    {
        $request->validate([
            'channel_name' => 'required|string',
        ]);

        $appId = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->channel_name;
        $channelName = 'call_' . auth()->id() . '_' . time();

        // Use logged-in user ID
        $uid = auth()->id() ?? rand(1000, 9999);

        $role = RtcTokenBuilder::ROLE_PUBLISHER;
        $expireTime = env('AGORA_TOKEN_EXPIRE', 3600);

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $expireTime
        );

        return response()->json([
            'status' => true,
            'data' => [
                'app_id' => $appId,
                'token' => $token,
                'channel_name' => $channelName,
                'uid' => $uid,
            ]
        ]);
    }
    
    public function generateToken(Request $request)
    {
        $request->validate([
            'channel_name' => 'nullable',
        ]);
    
        $appId = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->channel_name;
        $channelName = 'call_' . auth()->id() . '_' . time();
    
        $uid = auth()->id() ?? rand(1000, 9999);
    
        $expire = env('AGORA_TOKEN_EXPIRE', 3600);
    
        $token = AccessToken2::buildToken(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $expire
        );
    
        return response()->json([
            'status' => true,
            'data' => [
                'app_id' => $appId,
                'token' => $token,
                'channel_name' => $channelName,
                'uid' => $uid,
            ]
        ]);
    }
}