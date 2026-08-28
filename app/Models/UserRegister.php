<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserRegister extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'global_auth';

    protected $table = 'user_register';
}

?>