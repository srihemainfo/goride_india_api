<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    protected $connection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // You can dynamically set the connection based on logic here if needed
        $this->setConnection(config('database.default'));
    }

    /**
     * Define roles relationship for dynamic connections.
     */
    public function roles(): BelongsToMany
    {
        // Use dynamic connection based on the instance connection
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
            ->on($this->connection);
    }
    
    
    
    // public function roles(){
    //     return $this->belongsToMany(Role::class,'role_user','user_id','role_id');
    // }
    
}
