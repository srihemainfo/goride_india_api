<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = "area";

    protected $fillable = [
        'place_id',
        'area',
        'address',
        'city',
        'pincode',
        'status',
        'p_extra',
        'd_extra',
    ];
}
