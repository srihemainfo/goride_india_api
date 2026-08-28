<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Partners extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'mysql1';
    protected $table = 'partnerlists';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profession',
        'affiliation_type',
        'add_mobile_app',
        'websites',
        'socialmedia_accounts',
        'register_as',
        'agree_terms',
        'db_port',
        'db_host',
        'database_name',
        'database_user',
        'database_password',
        'db_keyword',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
