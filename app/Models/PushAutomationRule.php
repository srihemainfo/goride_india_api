<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushAutomationRule extends Model
{
    protected $fillable = [
        'event',
        'delay_minutes',
        'title',
        'redirect',
        'message',
        'conditions',
        'is_active'
    ];

    protected $casts = [
        'conditions' => 'array'
    ];
}