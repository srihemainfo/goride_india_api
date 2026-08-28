<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PushNotificationService
{
    public $serviceAccountPath;
    public $serviceAccount;

    public function __construct()
    {
        $this->serviceAccountPath = storage_path(
            'app/firebase/firebase-config-customer.json'
        );

        $this->serviceAccount = json_decode(
            file_get_contents($this->serviceAccountPath),
            true
        );
    }

    public function getAccessToken()
    {
        if (Cache::has('firebase_access_token')) {

            return Cache::get('firebase_access_token');
        }

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

        $claimSetEncoded = base64_encode(
            json_encode($claimSet)
        );

        $signatureInput = "$header.$claimSetEncoded";

        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private(
                $this->serviceAccount['private_key']
            ),
            OPENSSL_ALGO_SHA256
        );

        $jwt = "$signatureInput." .
            str_replace(
                ['+', '/', '='],
                ['-', '_', ''],
                base64_encode($signature)
            );

        $postFields = http_build_query([
            'grant_type' =>
                'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        $ch = curl_init(
            $this->serviceAccount['token_uri']
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);

        curl_close($ch);

        $responseData = json_decode($response, true);

        $token = $responseData['access_token'] ?? null;

        if ($token) {

            Cache::put(
                'firebase_access_token',
                $token,
                now()->addMinutes(55)
            );
        }

        return $token;
    }

    public function send(
        $userId,
        $title,
        $body,
        $data = []
    ) {

        $tokens = DB::table('customer_register')->where('id', $userId)
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            return false;
        }

        $accessToken = $this->getAccessToken();

        $firebase = new FirebaseJobService(
            $this->serviceAccount['project_id'],
            $accessToken
        );

        foreach ($tokens as $token) {

            $firebase->sendNotification(
                $token,
                $title,
                $body,
                $data
            );
        }

        return true;
    }
}