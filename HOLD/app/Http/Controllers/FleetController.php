<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fleet;
use App\Models\CarFare;
use Illuminate\Support\Facades\{Validator, DB};
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class FleetController extends Controller
{
    private $module = 'LIST_FLEETS_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index(Request $request)
    {
        // dd(session('redirect_url'));
        if (session('redirect_url') != '/fleet') {
            
            return redirect(session('redirect_url'));
            
        } else {
            
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

            //UI permissions array destructured
            [
                'CREATE' => $IS_CREATABLE,
                'UPDATE' => $IS_UPDATABLE,
                'DELETE' => $IS_DELETABLE
            ] = $this->permission->ui_permissions($this->module);
    
            if ($request->ajax()) {
                $data = DB::table('vehicle')->select('*');
    
                // ->whereNull('roles.deleted_at')
                // ->latest()->get();
    
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function ($row) use ($IS_UPDATABLE) {
                        $Active = $row->status == "Active" ? "SELECTED" : "";
                        $Inactive = $row->status == "Inactive" ? "SELECTED" : "";
                        
                        if ($IS_UPDATABLE) {
                            $status = "<select class=\"form-control fleet-status\" name=\"status\" id=\"status\" data-id=\"$row->id\" data-previous=\"$row->status\"> <option value=\"Active\" $Active >Active</option> <option value=\"Inactive\" $Inactive >Inactive</option> </select>";
                        } else {
                            $status = $row->status;
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                        $btn = '';
    
                        if ($IS_UPDATABLE) {
                            $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editFleet"><i class="fa fa-edit"></i></a>';
                        }
                        if ($IS_DELETABLE) {
                            $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteFleet"><i class="fa fa-trash"></i></a>';
                        }
                            return $btn;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
    
            return view('fleets.index', compact('IS_CREATABLE'));
            
        }
        
        
    }
    
    public function firstFleet(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        
        $bookingset = DB::table('bookingsetting')->select('*')->first();

        if ($request->ajax()) {
            $data = DB::table('vehicle')->select('*');

            // ->whereNull('roles.deleted_at')
            // ->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) use ($IS_UPDATABLE) {
                    $Active = $row->status == "Active" ? "SELECTED" : "";
                    $Inactive = $row->status == "Inactive" ? "SELECTED" : "";
                    
                    if ($IS_UPDATABLE) {
                        $status = "<select class=\"form-control fleet-status\" name=\"status\" id=\"status\" data-id=\"$row->id\" data-previous=\"$row->status\"> <option value=\"Active\" $Active >Active</option> <option value=\"Inactive\" $Inactive >Inactive</option> </select>";
                    } else {
                        $status = $row->status;
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                    $btn = '';

                    if ($IS_UPDATABLE) {
                        $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editFleet"><i class="fa fa-edit"></i></a>';
                    }
                    if ($IS_DELETABLE) {
                        $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteFleet"><i class="fa fa-trash"></i></a>';
                    }
                        return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('fleets.firstFleet', compact('IS_CREATABLE', 'bookingset'));
    }


    public function store(Request $request)
    {
        // dd($request);
        if(isset($request->fleet_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $validator = Validator::make($request->all(), [
             'name' => [
                'required',
                'max:50',  
                'regex:/^[a-zA-Z]+$/', 
                Rule::unique('vehicle')->ignore($request->fleet_id),
            ],

            "passenger" => ["required", "numeric"],
            "min" => ["required", "numeric"],
            "max" => ["required", "numeric"],
            "luggage" => ["required", "numeric"],
            "hand_luggage" => ["required", "numeric"],
            "no_of_seats" => ["required", "numeric"],
            "child" => ["required", "numeric"],
            "order" => ["numeric"],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            if (!empty($request->fleet_id)) {
                $old_find_name = Fleet::find($request->fleet_id);
                
                if ($old_find_name) {
                    $old_find_name = strtolower($old_find_name->name);
            
                    // Alter Table column name
                    $request->name = str_replace(' ', '_', $request->name);
                    $alter_tbl = CarFare::alter($old_find_name, $request->name);
                }
            }
            
            $data = Fleet::updateOrCreate(
                ['id' => $request->fleet_id],
                [
                    "name" => $request->name,
                    "passenger" => $request->passenger,
                    "min" => $request->min,
                    "max" => $request->max,
                    "luggage" => $request->luggage,
                    "hand_luggage" => $request->hand_luggage,
                    "no_of_seats" => $request->no_of_seats,
                    "child" => $request->child,
                    "order" => $request->order,
                ]
            );
            
            

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }


    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $fleet = Fleet::find($id);
        return response()->json($fleet ? ['status' => 200, 'data' => $fleet] : ['status' => 400, 'data' => NULL]);
    }


    public function destroy(Fleet $fleet)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $fleet->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }


    public function FleetStatusUpdate(Request $request){
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $data = Fleet::updateOrCreate(['id' => $request->id], ['status' => $request->isActive]);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }
}
