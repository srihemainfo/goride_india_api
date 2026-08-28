<?php

namespace App\Services\Agora;

class RtcTokenBuilder
{
    const ROLE_ATTENDEE = 0;
    const ROLE_PUBLISHER = 1;

    public static function buildTokenWithUid(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $role,
        $expireTimestamp
    ) {
        $currentTimestamp = time();
        $privilegeExpiredTs = $currentTimestamp + $expireTimestamp;

        return self::buildTokenWithUidAndPrivilege(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs
        );
    }

    private static function buildTokenWithUidAndPrivilege(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $role,
        $privilegeExpiredTs
    ) {
        $key = $appCertificate;
        $message = $appId . $channelName . $uid . $privilegeExpiredTs;

        $signature = hash_hmac('sha256', $message, $key, true);

        return base64_encode($appId . ':' . $signature . ':' . $privilegeExpiredTs);
    }
}