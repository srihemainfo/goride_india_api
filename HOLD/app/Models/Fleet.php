<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    use HasFactory;

    protected $table = 'vehicle';

    protected $fillable = [
        'name',
        'passenger',
        'min',
        'max',
        'luggage',
        'hand_luggage',
        'booster',
        'child',
        'order',
        'status',
        'no_of_seats'
    ];
}
