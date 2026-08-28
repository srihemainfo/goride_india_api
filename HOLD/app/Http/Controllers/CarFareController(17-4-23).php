<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarFare;
use Illuminate\Support\Facades\DB;
use App\Services\Permissions\PermissionHelperService;

class CarFareController extends Controller
{
    private $module = 'CAR_FARE_MODULE';
    private $permission;
   
    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_car_fares = DB::table('car_fares')->select('*')->get();
        return view('carfares.index', compact('list_car_fares', 'IS_UPDATABLE'));
    }

    public function store(Request $request)
    {
        if(isset($request->fare_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }
        $data = CarFare::updateOrCreate(
            ['id' => $request->fare_id],
            [
                'saloon' => sanitize_amount_input($request->saloon),
                'executive' => sanitize_amount_input($request->executive),
                'estate' => sanitize_amount_input($request->estate),
                'mpv' => sanitize_amount_input($request->mpv),
                'mpv5' => sanitize_amount_input($request->mpv5),
                'mpv6' => sanitize_amount_input($request->mpv6),
                'mpv8' => sanitize_amount_input($request->mpv8),
                'mpv_executive' => sanitize_amount_input($request->mpv_executive),
            ]
        );

        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }
}
