<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarFare extends Model
{
    use HasFactory;

    protected $table = 'car_fare';
   protected $guarded = []; 
    public function updateFields(array $fields)
    {
        // Update the model with the provided fields
        $this->update($fields);
    }

    // protected $fillable = [
       
    //     'start',
    //     'end',
    //     'rate',
    //     'distance',
    //     'created_at',
    //     'updated_at',
    //     'deleted_at',
        
    // ];
}
