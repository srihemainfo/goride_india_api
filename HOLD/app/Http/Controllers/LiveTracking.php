<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Permissions\PermissionHelperService;

class LiveTracking extends Controller
{
    private $module = 'LIVE_TRACKING_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        return view('tracking.index');
    }

    public function GetMovingDrivers(Request $request)
    {
        $terms = $searchstring = $request->search;

        $terms = str_replace(" ", "+", "$terms");

        $drivers_from_account = DB::table('bookinfo')
            ->select('bookinfo.driver_id AS driver_id', 'bookinfo.job_no AS job_id', 'driver.name AS driver_name')
            ->join('driver', 'bookinfo.driver_id', '=', 'driver.id')
            ->where('order_status', '=', 'Moving')
            ->where('name', 'LIKE', '%' . $searchstring . '%')
            ->get();

        $arr = [];

        $j = 0;
        foreach ($drivers_from_account as $item) {
            $arr[] = array(
                'id' => $item->driver_id,
                'label' => $j,
                'text' => $item->driver_name .' ('.$item->job_id.')'
            );
            $j++;
        }

        $i = 6;

        return response()->json($arr);
    }
}
