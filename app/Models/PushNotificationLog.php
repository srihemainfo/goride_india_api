<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'rule_id',
        'event',
        'status',
        'sent_at'
    ];
}