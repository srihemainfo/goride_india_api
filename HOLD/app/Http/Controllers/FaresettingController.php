<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\Area;

use Illuminate\Support\Facades\{Validator, DB};

use Yajra\DataTables\Facades\DataTables;

use App\Services\Permissions\PermissionHelperService;



class FaresettingController extends Controller

{

    private $module = 'LIST_AREAS_MODULE';

    private $permission;



    public function __construct()

    {

        $this->permission = new PermissionHelperService;

    }

    public function faresetting(Request $request){
        return view('area.faresetting_index');
     }

}

