<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarFare extends Model
{
    use HasFactory;

    protected $table = 'car_fares';

    protected $fillable = [
        'saloon',
        'executive',
        'estate',
        'mpv',
        'mpv5',
        'mpv6',
        'mpv8',
        'mpv_executive',
    ];
}
