<?php

namespace App\Services;

class PusherService
{
    public static function trigger(string $channel, string $event, array $data): void
    {
        $appId   = env('PUSHER_APP_ID');
        $key     = env('PUSHER_APP_KEY');
        $secret  = env('PUSHER_APP_SECRET');
        $cluster = env('PUSHER_APP_CLUSTER');

        $path = "/apps/{$appId}/events";

        $body = json_encode([
            'name'     => $event,
            'channels' => [$channel],
            'data'     => json_encode($data),
        ]);

        $query = http_build_query([
            'auth_key'       => $key,
            'auth_timestamp' => time(),
            'auth_version'   => '1.0',
            'body_md5'       => md5($body),
        ]);

        $stringToSign = "POST\n{$path}\n{$query}";
        $signature = hash_hmac('sha256', $stringToSign, $secret);

        $url = "https://api-{$cluster}.pusher.com{$path}?{$query}&auth_signature={$signature}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
        ]);

        curl_exec($ch);
        curl_close($ch);
    }
}