<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourlyPackage extends Model
{
    use HasFactory;

    protected $table = 'hourly_package';

    protected $fillable = [
        
        'Distance',	'Hours',	'Saloon',	'Executive',	'MPV',	 'seater'
      
    ];
}
