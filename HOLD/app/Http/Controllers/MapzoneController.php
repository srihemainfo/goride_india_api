<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\LocationRange;

use Illuminate\Support\Facades\{Validator, DB};

use Yajra\DataTables\Facades\DataTables;

use App\Services\Permissions\PermissionHelperService;



class MapzoneController extends Controller

{

    private $module = 'LOCATION_RANGE_MODULE';

    private $permission;



    public function __construct()

    {

        $this->permission = new PermissionHelperService;

    }



    public function mapzone(Request $request)

    {

        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);



       
        [

            'CREATE' => $IS_CREATABLE,

            'UPDATE' => $IS_UPDATABLE,

            'DELETE' => $IS_DELETABLE

        ] = $this->permission->ui_permissions($this->module);

 
        return view('offerdays.mapzone.index');

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

