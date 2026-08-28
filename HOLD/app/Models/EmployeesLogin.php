<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EmployeesLogin extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'mysql1';
    protected $table = 'employees';

    protected $fillable = [
        'email',
        'password',
        'partner_id',
        'token',
        'app_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
