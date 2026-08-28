<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideFeedback extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ride_id',
        'driver_id',
        'customer_id',
        'rating',
        'review',
        'issue_type',
        'issue_description',
        'priority',
        'tags',
        'is_flagged'
    ];
    
    protected $casts = [
        'tags' => 'array'
    ];
}