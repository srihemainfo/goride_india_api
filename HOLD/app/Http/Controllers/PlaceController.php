<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Facades\{Validator, DB};
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class PlaceController extends Controller
{
    private $module = 'LIST_PLACES_MODULE';
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

        if ($request->ajax()) {
            $data = DB::table('place')
                ->select('*');
            // ->whereNull('roles.deleted_at')
            // ->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) use ($IS_UPDATABLE) {
                    $Active = $row->status == "Active" ? "SELECTED" : "";
                    $Pending = $row->status == "Pending" ? "SELECTED" : "";
                    if ($IS_UPDATABLE) {
                        $status = "<select class=\"form-control place-status\" name=\"status\" id=\"status\" data-id=\"$row->id\" data-previous=\"$row->status\"> <option value=\"Active\" $Active >Active</option> <option value=\"Pending\" $Pending >Pending</option> </select>";
                    } else {
                        $status = $row->status;
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                    $btn = '';
                    if ($IS_UPDATABLE) {
                        $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPlace"><i class="fa fa-edit"></i></a>';
                    }
                    if ($IS_DELETABLE) {
                        $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deletePlace"><i class="fa fa-trash"></i></a';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('places.index', compact('IS_CREATABLE'));
    }


    public function store(Request $request)
    {
        if(isset($request->place_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }
        
        $validator = Validator::make($request->all(), [
            "place" => ["required", Rule::unique('place')->ignore($request->place_id)],
            "discount" => ["required", "numeric"],
            "discount_type" => ["required"]
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            $data = Place::updateOrCreate(
                ['id' => $request->place_id],
                [
                    'place' => $request->place,
                    'discount' => $request->discount,
                    'discount_type' => $request->discount_type,
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }


    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $place = Place::findOrFail($id);
        return response()->json($place ? ['status' => 200, 'data' => $place] : ['status' => 400, 'data' => NULL]);
    }


    public function destroy(Place $place)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $place->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }

    public function PlaceStatusUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $data = Place::updateOrCreate(['id' => $request->id], ['status' => $request->isActive]);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }
}
