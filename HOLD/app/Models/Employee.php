<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes; //for soft delete

    protected $table = "employees";

    protected $fillable = [
        'emp_full_name',
        'status',
        'email',
        'phone',
        'user_id',
    ];
    protected $dates = [ 'deleted_at' ];//for soft delete

}
