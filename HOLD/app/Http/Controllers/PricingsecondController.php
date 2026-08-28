<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Generalsetting;
use Illuminate\Support\Facades\DB;
use App\Services\Permissions\PermissionHelperService;

class PricingsecondController extends Controller
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

    // UI permissions array destructured
    [
        'CREATE' => $IS_CREATABLE,
        'UPDATE' => $IS_UPDATABLE,
        'DELETE' => $IS_DELETABLE
    ] = $this->permission->ui_permissions($this->module);

    $list_car_fares = DB::table('hourly_package')->select('*')->get();

    return view('pricing-list.Hourly_package.index' ,compact('list_car_fares', 'IS_UPDATABLE','IS_CREATABLE','IS_DELETABLE'));
    
      
    }
   public function showdata()
    {
        
  $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

    // UI permissions array destructured
    [
        'CREATE' => $IS_CREATABLE,
        'UPDATE' => $IS_UPDATABLE,
        'DELETE' => $IS_DELETABLE
    ] = $this->permission->ui_permissions($this->module);

    // $list_car_fares = DB::table('hourly_package')->select('*')->get();

    return view('pricing-list.Locationcategory.index' );
    
      
    }

   
}
