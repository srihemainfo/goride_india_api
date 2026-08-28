<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Generalsetting;
use Illuminate\Support\Facades\DB;
use App\Services\Permissions\PermissionHelperService;

class PricingController extends Controller
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

        $list_car_fares = DB::table('general_pricing')->select('*')->get();
       // dd($list_car_fares);
        return view('pricing-list.general.index', compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    }
    
    
    public function VehiclePricingView()
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
    
      public function DistanceSlab()
    {
        
      //  dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_car_fares = DB::table('distance_slab')->select('*')->get();
       // dd($list_car_fares);
        return view('pricing-list.distanceslab.index', compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    }
    
    
    public function ListFleet()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_car_fares = DB::table('vehicle')->select('*')->get();
       // dd($list_car_fares);
        return view('tools.fleets.index', compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    }
    
     public function FixedPrice()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_car_fares = DB::table('fixed_pricing')->select('*')->get();
       // dd($list_car_fares);
        return view('pricing-list.fixedprice.index', compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    }

    public function EmailTemplate()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_car_fares = DB::table('email_templates')->select('*')->get();
       // dd($list_car_fares);
        return view('emailTemplate.index', compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    }
    
    public function email_builder(Request $request){
        return view('emailTemplate.builder');
    }
   
}
