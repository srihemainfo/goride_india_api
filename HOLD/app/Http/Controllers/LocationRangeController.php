<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocationRange;
use Illuminate\Support\Facades\{Validator, DB};
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class LocationRangeController extends Controller
{
    private $module = 'LOCATION_RANGE_MODULE';
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

        $list_places = DB::table('place')->select('id', 'place')
            ->where('status', '=', 'Active')
            ->get();

        // if ($request->ajax()) {
        //     $data = DB::table('fare_details')
        //         ->join('place', 'place.id', '=', 'fare_details.type')
                
        //         ->whereNull('fare_details.deleted_at')
        //         ->select('fare_details.*', 'place.place');


        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         ->filter(function ($query) use ($request) {
        //             if ($request->has('name')) {
        //                 $query->where('name', 'like', "%{$request->get('name')}%");
        //             }

        //             if ($request->has('type')) {
        //                 $query->where('type', 'like', "%{$request->get('type')}%");
        //             }

        //             if ($request->has('from_charge')) {
        //                 $query->where('from_charge', 'like', "%{$request->get('from_charge')}%");
        //             }

        //             if ($request->has('to_charge')) {
        //                 $query->where('to_charge', 'like', "%{$request->get('to_charge')}%");
        //             }

        //             if ($request->has('passing_charge')) {
        //                 $query->where('passing_charge', 'like', "%{$request->get('passing_charge')}%");
        //             }
        //         })
        //         ->addColumn('status', function ($row) use ($IS_UPDATABLE){
        //           //dd($row->status) ;
        //             $status = '';
        //             if ($IS_UPDATABLE) {
        //                 if ($row->status == "1") {
        //                     $status = "<select class=\"form-control location-range-status\" name=\"status\" data-id=\"$row->id\" data-previous=\"$row->status\"> <option value=\"1\">Active</option> <option value=\"0\">Inactive</option></select>";
        //                 } elseif ($row->status == "0") {
        //                     $status = "<select class=\"form-control location-range-status\" name=\"status\" data-id=\"$row->id\" data-previous=\"$row->status\"> <option value=\"0\">Inactive</option> <option value=\"1\">Active</option> </select>";
        //                 }
        //             } else {
        //                 if ($row->status == "1") {
        //                     $status = "Active";
        //                 } elseif ($row->status == "0") {
        //                     $status = "Inactive";
        //                 }
        //             }
                
        //         return $status;
        //         })
        //         ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
        //             $btn = '';

        //             if ($IS_UPDATABLE) {
        //                 $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editLocationRange"><i class="fa fa-edit"></i></a>';
        //             }
        //             if ($IS_DELETABLE) {
        //                 $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteLocationRange"><i class="fa fa-trash"></i></a';
        //             }

        //             return $btn;
        //         })
        //         ->rawColumns(['action', 'status'])
        //         ->make(true);
        // }

        return view('offerdays.locationrange.index', compact('list_places', 'IS_CREATABLE', 'IS_UPDATABLE'));
    }

    public function create()
    {
        return view('offerdays.locationrange.draw_map');
    }

    public function store(Request $request)
    {
        if (isset($request->locationrange_id)) {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $validator = Validator::make($request->all(), [
            "name" => ["required"],
            'type' => ["required"],
            "from_charge" => ["nullable"],
            "to_charge" => ["nullable"],
            "passing_charge" => ["nullable"],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        } else {
            $data = LocationRange::updateOrCreate(
                ['id' => $request->locationrange_id],
                [
                    'name' => $request->name,
                    'type' => $request->type,
                    'from_charge' => sanitize_amount_input($request->from_charge),
                    'to_charge' => sanitize_amount_input($request->to_charge),
                    'passing_charge' => sanitize_amount_input($request->passing_charge),
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }


    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $locationrange = LocationRange::find($id);
        return response()->json($locationrange ? ['status' => 200, 'data' => $locationrange] : ['status' => 400, 'data' => NULL]);
    }


    public function destroy(LocationRange $locationrange)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $locationrange->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }

    public function GetZones(Request $request)
    {
        $searchstring = $request->zone_name;

        $drivers_from_account = DB::table('fare_details')
            ->select('id', 'name')
            ->where('name', 'LIKE', '%' . $searchstring . '%')
            ->whereNull('deleted_at')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        $arr = [];

        $j = 0;
        foreach ($drivers_from_account as $item) {
            $arr[] = array(
                'id' => $item->id,
                'label' => $j,
                'text' => $item->name
            );
            $j++;
        }

        return response()->json($arr);
    }

    public function UpdateCoordinates(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);

        $data = LocationRange::where('id', $request->zone_id)
            ->update(
                [
                    'coordinates' => $request->coordinates
                ]
            );

        $request->session()->put('coordinates_update', 'Coordinates are updated successfully.');

        return response()->json($data ? ['status' => 200, 'errors' => NULL] : ['status' => 400, 'errors' => NULL]);
    }

    public function LocationRangeStatusUpdate(Request $request){
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $data = LocationRange::updateOrCreate(['id' => $request->id], ['status' => $request->isActive]);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }

}
