<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected const ACTION_TYPE = [
        'index'   => 'READ',
        'create'  => 'CREATE',
        'store'   => 'CREATE',
        'edit'    => 'UPDATE',
        'update'  => 'UPDATE',
        'destroy' => 'DELETE'
    ];
}
