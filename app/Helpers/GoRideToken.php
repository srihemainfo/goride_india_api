<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class GoRideToken
{
    public static function generate()
    {
        $payload = [
            'rand' => Str::random(32),
            'exp'  => now()->addMinutes(5)->timestamp
        ];

        $encrypted = Crypt::encryptString(json_encode($payload));

        return 'GORIDE|' . $encrypted;
    }

    public static function validate($token)
    {
        if (!str_starts_with($token, 'GORIDE|')) {
            return false;
        }

        $encrypted = explode('|', $token)[1];

        try {
            $payload = json_decode(Crypt::decryptString($encrypted), true);

            if (!$payload || now()->timestamp > $payload['exp']) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
}