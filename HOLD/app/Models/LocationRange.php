<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LocationRange extends Model
{
    use HasFactory;
    use SoftDeletes; //for soft delete

    protected $table = "fare_details";

    protected $fillable = [
        'type',
        'name',
        'coordinates',
        'createdon',
        'status',
        'deletes',
        'from_charge',
        'to_charge',
        'passing_charge',
        
    ];
    protected $dates = [ 'deleted_at' ];//for soft delete


}
