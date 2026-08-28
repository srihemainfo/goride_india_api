<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offertime extends Model
{
    use HasFactory;

    protected $table = "special_time";

    protected $fillable = [
        'cost',
        'from',
        'to',
        'content',
    ];
}
