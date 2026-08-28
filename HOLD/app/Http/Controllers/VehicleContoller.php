<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Services\Permissions\PermissionHelperService;

class VehicleController extends Controller
{
    private $module = 'CAR_FARE_MODULE';
    private $permission;
   
    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index()
    {
        
      //  dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_car_fares = DB::table('vehicle_pricing')->select('*')->get();
       // dd($list_car_fares);
        return view('pricing-list.vehicle.index', compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    }


   
}
