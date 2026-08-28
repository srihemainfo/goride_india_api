<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offerday extends Model
{
    use HasFactory;

    protected $table = 'special_price';

    protected $fillable = [
        'cost',
        'dates',
        'content',
    ];
}
