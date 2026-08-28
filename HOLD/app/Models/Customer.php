<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = "account";

    protected $fillable = [
        'f_name',
        'company_name',
        'email',
        'phone',
        'address1',
        'remark',
    ];
}
