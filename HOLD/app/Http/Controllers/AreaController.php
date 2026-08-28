<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use Illuminate\Support\Facades\{Validator, DB};
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class AreaController extends Controller
{
    private $module = 'LIST_AREAS_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }
    
    public function index(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        $list_places = DB::table('place')->select('id', 'place')->get();

        // dd($data);

        if ($request->ajax()) {
            $data = DB::table('area')
                ->join('place', 'place.id', '=', 'area.place_id')
                ->select('area.*', 'place.place');
            // ->whereNull('roles.deleted_at')

            return DataTables::of($data)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('area_name_filter')) {
                        $query->where('area', 'like', "%{$request->get('area_name_filter')}%");
                    }

                    if ($request->has('selected_place')) {
                        $query->where('place_id', '=', $request->get('selected_place'));
                    }
                })
                ->addColumn('p_extra', function ($row) use ($IS_UPDATABLE) {
                    if ($IS_UPDATABLE) {
                        $pickup = "<div class=\"input-group\">
                                    <input type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\" value=\"$row->p_extra\">
                                    <div class=\"input-group-append\">
                                        <button title=\"Update pickup extra\" data-id=\"$row->id\" data-name=\"pickup\" class=\"btn btn-sm btn-success btn_icon update_p_extra\" id=\"\" type=\"button\"><i class=\"fa fa-check\"></i></button>
                                    </div>
                                </div>";
                    }else {
                        $pickup = $row->p_extra;
                    }

                    return $pickup;
                })
                ->addColumn('d_extra', function ($row) use ($IS_UPDATABLE) {
                    if ($IS_UPDATABLE) {
                        $drop = "<div class=\"input-group\">
                                <input type=\"text\" class=\"form-control form-control-30 d_extra\" placeholder=\"\" value=\"$row->d_extra\">
                                <div class=\"input-group-append\">
                                    <button title=\"Update drop extra\" data-id=\"$row->id\" data-name=\"drop\" class=\"btn btn-sm btn-success btn_icon update_d_extra\" id=\"\" type=\"button\"><i class=\"fa fa-check\"></i></button>
                                </div>
                            </div>";
                        } else {
                            $drop = $row->d_extra;
                        }
                    return $drop;
                })
                ->addColumn('status', function ($row) use ($IS_UPDATABLE) {
                    $Active = $row->status == "Active" ? "SELECTED" : "";
                    $Pending = $row->status == "Pending" ? "SELECTED" : "";
                    
                    if ($IS_UPDATABLE) {
                        $status = "<select class=\"form-control area-status\" name=\"status\" id=\"status\" data-id=\"$row->id\" data-previous=\"$row->status\"> <option value=\"Active\" $Active >Active</option> <option value=\"Pending\" $Pending >Pending</option> </select>";
                    } else {
                        $status = $row->status;
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                    $btn = '';
                    if ($IS_UPDATABLE) {
                        $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editArea"><i class="fa fa-edit"></i></a>';
                    }
                    if ($IS_DELETABLE) {
                        $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteArea"><i class="fa fa-trash"></i></a';
                    }
                    return $btn;
                })
                ->rawColumns(['p_extra', 'd_extra', 'status', 'action'])
                ->make(true);
        }

        return view('area.index', compact('list_places', 'IS_CREATABLE'));
    }


    public function store(Request $request)
    {   
        if(isset($request->id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                "place_name" => ["required"],
                "area_name" => ["required"],
                "address" => ["nullable"],
                "city" => ["nullable"],
                "post_code" => ["nullable"],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            $data = Area::updateOrCreate(
                ['id' => $request->id],
                [
                    'place_id' => $request->place_name,
                    'area' => $request->area_name,
                    'address' => $request->address,
                    'city' => $request->city,
                    'pincode' => $request->post_code,
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }


    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $area = Area::find($id);
        return response()->json($area ? ['status' => 200, 'data' => $area] : ['status' => 400, 'data' => NULL]);
    }


    public function destroy(Area $area)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $area->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }


    public function AreaStatusUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $data = Area::updateOrCreate(['id' => $request->id], ['status' => $request->isActive]);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }


    public function ExtraAmountUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $update_data = [];

        if ($request->type === 'drop') {
            $update_data = ['d_extra' => !empty($request->extra) && is_numeric($request->extra) ? $request->extra : 0];
        } elseif ($request->type === 'pickup') {
            $update_data = ['p_extra' => !empty($request->extra) && is_numeric($request->extra) ? $request->extra : 0];
        }

        $data = Area::updateOrCreate(['id' => $request->id], $update_data);

        return response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }
}
