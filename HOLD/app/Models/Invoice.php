<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    //use SoftDeletes; //for soft delete

    protected $table = "invoiceno";

    protected $fillable = [
        'memberid',
        'jobid',
        'clientname',
        'clientaddress',
        'invdate',
        'invoiceno',
        'pay_type',
        'net',
        'tax_per',
        'total',
        'date_time',
        'address',
        'description',
        'status',
        
        
    ];
    //protected $dates = [ 'deleted_at' ];//for soft delete

}
