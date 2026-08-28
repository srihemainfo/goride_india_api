<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocuments extends Model
{
    use HasFactory;

    protected $table = "driver_documents";

    protected $fillable = [
        'driver_id',
        'description',
        'file_path',
               
    ];

    public function createDriverDocuments($input)
    {
        $input = (object) $input;

        // var_dump("<pre>", $input);
        // die;

        return DriverDocuments::create([
            'driver_id' => $input->driver_id,
            'description' => $input->description,
            'file_path' => $input->file_path,
            
        ]);
    }


}
