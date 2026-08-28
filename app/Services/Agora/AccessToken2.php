<?php

namespace App\Services\Agora;

class AccessToken2
{
    const VERSION = "007";

    public static function buildToken(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $expire
    ) {
        $issueTs = time();
        $salt = rand(1, 99999999);

        $ts = $issueTs + $expire;

        $message = $appId . $channelName . $uid . $ts;

        $signature = hash_hmac("sha256", $message, $appCertificate, true);

        return self::VERSION . base64_encode(
            $appId . $signature . $ts . $salt
        );
    }
}