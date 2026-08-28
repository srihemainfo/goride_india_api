<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $table = 'admin_form';

    // protected $fillable = [
    //     'place_from',
    //     'area_from',
    //     'place_to',
    //     'area_to',
    //     'fromid',
    //     'toid',
    //     'saloon',
    //     'estate',
    //     'mpv',
    //     'mpv6',
    //     'mpv8',
    //     'executive',
    //     'mpv5',
    //     'mpv_executive',
    // ];
    
    protected $fillable = [
        'place_from',
        'area_from',
        'place_to',
        'area_to',
        'fromid',
        'toid',
        'sedan',
        'minivan',
        'seater7',
        'seater8',
        'executive',
        'mpv_executive',
        'sharedride',
        'seater32',
        'seater44',
        'sixteenseater',
        'twotwoseater',
        'seater55',
    ];
    
}
