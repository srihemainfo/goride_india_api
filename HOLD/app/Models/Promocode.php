<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;

    protected $table = "promo_code";

    protected $fillable = [
        'code',
        'minvalue',
        'maxvalue',
        'fromdate',
        'todate',
        'type',
        'values',
    ];
}
