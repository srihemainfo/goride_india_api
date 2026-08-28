<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Offertime extends Model
{
    use HasFactory, Auditable;

    protected $table = "special_time";

    protected $fillable = [
        'cost',
        'from',
        'to',
        'content',
    ];
}
