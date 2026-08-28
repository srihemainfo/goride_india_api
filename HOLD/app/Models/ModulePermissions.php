<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermissions extends Model
{
    use HasFactory;

    protected $table = "module_permissions";

    protected $fillable = [
        'is_readable',
        'is_creatable',
        'is_updatable',
        'is_deletable',
    ];
}
