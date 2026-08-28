<?php



namespace App\Http\Controllers\crm;



use App\Http\Controllers\Controller;

use App\Http\Requests\{ DriverStoreRequest, DriverUpdateRequest };

use App\Models\{ Driver, DriverDocuments };

use Illuminate\Support\Facades\{ File, DB, Hash, Validator };

use Illuminate\Validation\Rules;

use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

use Yajra\DataTables\Facades\DataTables;

use App\Services\Driver\ExcelExportService;

use App\Services\Permissions\PermissionHelperService;

use Exception;

use App\Http\Controllers\crm\ExtrasController;



class DriverController extends Controller {

    private $module = 'DRIVER_MODULE';

    private $driver;

    private $driver_documents;

    private $excel_export;

    private $permission;



    public function __construct() {

        $this -> driver = new Driver();

        $this -> driver_documents = new DriverDocuments();

        $this -> excel_export = new ExcelExportService;

        $this -> permission = new PermissionHelperService;

    }





    public function index(Request $request) {

        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['index'], $check[0] -> db_key);



            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this -> permission -> ui_permissions($this -> module, $check[0] -> db_key);



            $status = ($request->status == '' || $request->status == 'All' ) ? '!=' : $request -> status;

            $data = DB:: connection($check[0] -> db_key) -> table('driver') -> select('*') -> where('status', $status, $request ->status) -> whereNull('deleted_at') -> orderBy('id', 'desc') -> get();

            // $brand = DB:: table('brands') -> select('*') -> orderBy('brand', 'asc') -> get();

            // $model = DB:: table('car_models') -> select('*') -> orderBy('model_name', 'asc') -> get();

            // return $brand;

            return response() -> json($data ? ['status' => 200, 'data' => $data, 'brands' => []] : ['status' => 400, 'data' => NULL]);

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }

    }





    public function create() {

        $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['create']);

        $isEditable = false;

        $vehicle = DB:: table('vehicle') -> where('status', 'Active') -> get();

        $driver = DB:: table('driver') -> orderby('name', 'asc') -> get();

        return view('drivers.create', compact('isEditable', 'vehicle', 'driver'));

    }





    public function store(Request $request) {



        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['store'], $check[0] -> db_key);

            $validator = Validator:: make($request -> all(), [

                "driver_no" => ["required", Rule:: unique($check[0] -> db_key.'.driver') -> ignore($request -> driver_id)],

                "name" => ["required"],

                "email" => ["required", "email", Rule:: unique($check[0] -> db_key.'.driver') -> ignore($request -> driver_id)],

                "phone" => ["required",Rule::unique($check[0]->db_key . '.driver')->whereNull('deleted_at')->ignore($request->driver_id)],

                // "password" => ["required"],

                "vehicle_type" => ["required"],

                "upload_photo" => ["nullable", "mimes:jpg,jpeg,png"],

                "address" => ["nullable"],

                "commision_value" => ["nullable", "numeric"],

                "driver_booking_percentage" => ["nullable", "numeric"],

                "booking_email" => ["nullable", "email"],

                "national_insurance_no" => ["nullable"],

                "vehicle_reg_no" => ["nullable"],

                "vehicle_color" => ["nullable"],

                // "vehicle_make" => ["nullable"],

                // "vehicle_model" => ["nullable"],

                "number_of_seats" => ["nullable", "numeric"],



                "vehicle_insurance" => ["nullable"],

                "vehicle_insurance_expiry" => ["nullable"],

                "vehicle_license" => ["nullable"],

                "vehicle_license_expiry" => ["nullable"],

                "pco_license_no" => ["nullable"],

                "pco_license_no_expiry" => ["nullable"],

                "driver_license_no" => ["nullable"],

                "driver_license_no_expiry" => ["nullable"],

                "add_cerficate" => ["nullable"],

                "mot_no" => ["nullable"],

                "mot_no_expiry" => ["nullable"],



                "refresh_time" => ["nullable", "numeric"],

                "before_reminder_time" => ["nullable", "numeric"],

                "start_journey_gaptime" => ["nullable", "numeric"],

                "customer_call" => ["nullable"],



                "dob" => ["nullable"],

                "start_date" => ["nullable"],

                "end_date" => ["nullable"],

            ]);



            if ($validator -> fails()) {

                return response() -> json(['status' => 400, 'errors' => $validator -> errors()]);

            }

            $validated = $validator->validated();

            $image_path = '';

            if ($request -> hasFile('upload_photo')) {

                $extrasController = new ExtrasController();

                $response = $extrasController -> uploadfiledriver($request);

                $files = json_decode($response -> getContent(), true);

                // dd($files);

                $image_path = $files['image_name'];

                $validated['upload_photo'] = $image_path ?? '';



            }



            $validated['password'] = Hash:: make($request -> password);

            $validated['upload_photo'] = $image_path ?? '';

            // dd('dddd');



            if ($request -> vehicle_insurance_expiry) {

                $validated['vehicle_insurance_expiry'] = date('Y-m-d', strtotime($request -> vehicle_insurance_expiry));

            }



            if ($request -> vehicle_license_expiry) {

                $validated['vehicle_license_expiry'] = date('Y-m-d', strtotime($request -> vehicle_license_expiry));

            }



            if ($request -> pco_license_no_expiry) {

                $validated['pco_license_no_expiry'] = date('Y-m-d', strtotime($request -> pco_license_no_expiry));

            }



            if ($request -> driver_license_no_expiry) {

                $validated['driver_license_no_expiry'] = date('Y-m-d', strtotime($request -> driver_license_no_expiry));

            }



            if ($request -> mot_no_expiry) {

                $validated['mot_no_expiry'] = date('Y-m-d', strtotime($request -> mot_no_expiry));

            }



            //  dd($image_path);

            // Driver::create($validated);

            $driver = $this -> driver -> createDriver($validated, $check[0] -> db_key);

            // dd($driver);

            if ($driver -> id) {

                return response() -> json(['status' => 200, 'message' => 'Driver created successfully']);

            } else {

                return response() -> json(['status' => 400, 'message' => "Not Created"]);

            }

        } catch (Exception $e) {

            // dd($e->getmessage());

            return response() -> json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }





    public function show($id) {

        $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['index']);

        return view('drivers.show', ['driver' => Driver:: findOrFail($id), 'show' => true]);

    }





    public function edit(Request $request) {

        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['edit'], $check[0] -> db_key);



            $document_details = DB:: connection($check[0] -> db_key) -> table('driver_documents')

                -> select('*')

                -> where('driver_id', '=', $request -> driver_id)

                -> get();

            // dd($document_details);

            $vehicle = DB:: connection($check[0] -> db_key) -> table('vehicle') -> where('status', 'Active') -> get();

            $driver = DB:: connection($check[0] -> db_key) -> table('driver') -> where('id', $request -> driver_id) -> get();

            // dd($vehicle);

            if ($driver -> count() > 0) {

                return response() -> json(['status' => 200, 'driver' => $driver, 'isEditable' => true, 'driver_id' => $request -> driver_id, 'document_details' => $document_details, 'vehicle' => $vehicle]);

            } else {

                return response() -> json(['status' => 400, 'error' => 'Data Not Found']);

            }

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }

    }





    public function update(Request $request) {

    

        try {

            // return $request->all();

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['update'], $check[0] -> db_key);

             $driverId = $request->input('driver_id');  // Ensure it's treated as an integer|

            $validator = Validator:: make($request -> all(), [

                "driver_no" => ["required", Rule:: unique($check[0] -> db_key.'.driver') -> ignore($driverId)],

                "name" => ["required"],

                "email" => ["required", "email", Rule:: unique($check[0] -> db_key.'.driver') -> ignore($driverId)],

                "phone" => ["required", Rule:: unique($check[0] -> db_key.'.driver') -> ignore($driverId)],

                // "password" => ["required"],

                "vehicle_type" => ["required"],

                "upload_photo" => ["nullable", "mimes:jpg,jpeg,png"],

                "address" => ["nullable"],

                "commision_value" => ["nullable", "numeric"],

                "driver_booking_percentage" => ["nullable", "numeric"],

                "booking_email" => ["nullable", "email"],

                "national_insurance_no" => ["nullable"],

                "vehicle_reg_no" => ["nullable"],

                "vehicle_color" => ["nullable"],

                "vehicle_make" => ["nullable"],

                "vehicle_model" => ["nullable"],

                "number_of_seats" => ["nullable", "numeric"],

                "add_cerficate" => ["nullable"],


                "vehicle_insurance" => ["nullable"],

                "vehicle_insurance_expiry" => ["nullable"],

                "vehicle_license" => ["nullable"],

                "vehicle_license_expiry" => ["nullable"],

                "pco_license_no" => ["nullable"],

                "pco_license_no_expiry" => ["nullable"],

                "driver_license_no" => ["nullable"],

                "driver_license_no_expiry" => ["nullable"],



                "mot_no" => ["nullable"],

                "mot_no_expiry" => ["nullable"],



                "refresh_time" => ["nullable", "numeric"],

                "before_reminder_time" => ["nullable", "numeric"],

                "start_journey_gaptime" => ["nullable", "numeric"],

                "customer_call" => ["nullable"],



                "dob" => ["nullable"],

                "start_date" => ["nullable"],

                "end_date" => ["nullable"],

            ]);



            if ($validator -> fails()) {

                return response() -> json(['status' => 400, 'errors' => $validator -> errors()]);

            }

                

            $image_path='';

            

            if ($request -> hasFile('upload_photo')) {

                $extrasController = new ExtrasController();

                $response = $extrasController -> uploadfiledriver($request);

                $files = json_decode($response -> getContent(), true);

        



                $image_path = $files['image_name'];

                //   dd($image_path);

                $validated['upload_photo'] = $image_path;



            }



            $validated = $validator -> validated();

            // $validated['profile_image_path'] = $request->hasFile('upload_photo') ? "/driver-upload/$ImageName" : $request->ExistingImagePath;

            // $validated['upload_photo'] = $image_path;



            if($image_path){

                $image_path=$image_path;

            }else{

              $image_path=$request->show_image_data;  

            }



            if ($request -> vehicle_insurance_expiry) {

                $validated['vehicle_insurance_expiry'] = date('Y-m-d', strtotime($request -> vehicle_insurance_expiry));

            }



            if ($request -> vehicle_license_expiry) {

                $validated['vehicle_license_expiry'] = date('Y-m-d', strtotime($request -> vehicle_license_expiry));

            }



            if ($request -> pco_license_no_expiry) {

                $validated['pco_license_no_expiry'] = date('Y-m-d', strtotime($request -> pco_license_no_expiry));

            }



            if ($request -> driver_license_no_expiry) {

                $validated['driver_license_no_expiry'] = date('Y-m-d', strtotime($request -> driver_license_no_expiry));

            }



            if ($request -> mot_no_expiry) {

                $validated['mot_no_expiry'] = date('Y-m-d', strtotime($request -> mot_no_expiry));

            }





            $driver = $this ->driver-> updateDriver($request -> driver_id, $validated, $check[0] -> db_key);

            if ($driver) {

                return response() -> json(['status' => 200, 'message' => 'Driver has been updated successfully', 'data' => $driver]);

            } else {

                return response() -> json(['status' => 400, 'message' => "Not Updated"]);

            }

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }



    }



    public function destroy(Request $request) {

        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['destroy'], $check[0] -> db_key);

            $data = Driver:: on($check[0] -> db_key) -> where('id', $request -> driver_id) -> delete ();

            return response() -> json($data ? ['status' => 200, 'message' => 'Driver has been deleted successfully'] : ['status' => 400, 'message' => 'Failed']);

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }

    }



    public function DriverStatusUpdate(Request $request) {

        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['update'], $check[0] -> db_key);

            $data = Driver:: on($check[0] -> db_key) -> where('id', $request -> driver_id) -> update(['status' => $request -> isActive]);

            return response() -> json($data ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }

    }





    public function DriverPasswordChange(Request $request) {

        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['update'], $check[0] -> db_key);

            $validator = Validator:: make($request -> all(), [

                'password' => ['required',

                    // 'confirmed',

                    Rules\Password:: defaults()],

            ]);

            if ($validator -> fails()) {

                return response() -> json([

                    'status' => 400,

                    'errors' => $validator -> errors()

                ]);

            } else {

                $data = Driver:: on($check[0] -> db_key) -> where('id', $request -> driver_id) -> update(['password' => Hash:: make($request -> password)]);

            }

            return response() -> json($data ? ['status' => 200, 'isUpdated' => true, 'errors' => NULL] : ['status' => 400, 'isUpdated' => false, 'errors' => NULL]);

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }

    }



    public function FileUpload(Request $request) {

        

        $validator = Validator:: make($request -> all(), [

            'select_file' => 'required|mimes:jpeg,png,jpg,pdf'

        ]);



        if ($validator -> fails()) {

            return response() -> json([

                'message' => $validator -> errors() -> all(),

                'uploaded_image' => '',

                'class_name' => 'alert-danger',

                'isUploaded' => false

            ]);

        } else {



            $image = $request -> file('select_file');

            

            $new_name = $request -> driver_id. "-".time(). '.'.$image -> getClientOriginalExtension();

            $image -> move(public_path('driver-documents/'.$request -> driver_id. ''), $new_name);



            $validated['driver_id'] = $request -> driver_id;

            $validated['description'] = $request -> description;

            $validated['file_path'] = $new_name;

            $driver_documents = $this -> driver_documents -> createDriverDocuments($validated);



            $document_details = DB:: table('driver_documents')

                -> select('*')

                -> where('driver_id', '=', $request -> driver_id)

                -> get();

            if ($driver_documents) {

                return response() -> json([

                    'message' => 'Documents Uploaded successfully',

                    'uploaded_image' => '<img src="/driver-documents/'.$request -> driver_id. '/'.$new_name. ' " class="img-thumbnail" width: "300" />',

                    'class_name' => 'alert-success',

                    'document_details' => $document_details,

                    'isUploaded' => true

                ]);

            }

        }

    }



    public function FileDelete(Request $request) {

        //dd($request);

        $documents = DB:: table('driver_documents')

            -> select('*')

            -> where('id', '=', $request -> document_id)

            -> get();

        if ($documents == true) {



            foreach($documents as $doc) {

                $delete_table = DB:: table('driver_documents') -> where('id', '=', $doc -> id) -> delete ();



                if ($delete_table == true && File:: exists('driver-documents/'.$doc -> driver_id. '/'.$doc -> file_path. '')) {

                    $delete_file = File:: delete ('driver-documents/'.$doc -> driver_id. '/'.$doc -> file_path. '');

                }

            }

        }

        $document_details = DB:: table('driver_documents')

            -> select('*')

            -> where('driver_id', '=', $doc -> driver_id)

            -> get();

        //dd($document_details);

        return response() -> json($delete_file ? ['status' => 200, 'isDeleted' => true, 'document_details' => $document_details] : ['status' => 400, 'isDeleted' => false, 'document_details' => $document_details]);

    }



    public function DriverExcelExport(Request $request) {

        $data = [];

        return $this -> excel_export -> driver_export($request);

    }



    public function MyDriver($booking_id) {

        $driver_details = DB:: table('bookinfo')

            -> leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')

            -> where('bookinfo.job_no', '=', $booking_id)

            -> get()

            -> first();



        $book_id = preg_replace('/[^0-9]/', '', $booking_id);



        // \DB::enableQueryLog();

        $pickup_points = DB:: table('pick_up_points') -> where('booking_id', '=', $book_id) -> get();

        //   dd(\DB::getQueryLog());



        $vehicle = DB:: table('vehicle') -> where('ref', '=', $driver_details -> vech_type) -> get();



        if (empty($driver_details)) {

            abort(404);

        }



        return view('drivers.my_driver', compact('driver_details', 'vehicle', 'pickup_points'));

    }



    public function DriverFilter(Request $request) {

        try {

            $check = Session:: get('check');

            $this -> permission -> check_privilege($this -> module, self:: ACTION_TYPE['index'], $check[0] -> db_key);

            $driver = new Driver();

            $data = $driver -> scopeFilterByCategory($check[0] -> db_key, $request -> all());

            return response() -> json($data -> count() > 0 ? ['status' => 200, 'data' => $data] : ['status' => 400, 'message' => 'Data Not Found']);

        } catch (Exception $e) {

            return response() -> json(['status' => 500, 'error' => $e -> getmessage()]);

        }

    }









public function Vehiclelist()

{

    try {

        $check = Session::get('check');

        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

        $loginn_id = $check[0]->id;



        [

            'CREATE' => $IS_CREATABLE,

            'UPDATE' => $IS_UPDATABLE,

            'DELETE' => $IS_DELETABLE

        ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);

        $vehicles = DB::connection($check[0]->db_key)

            ->table('vehicle') 

            ->select('*')

            ->get(); 

        if ($vehicles->isNotEmpty()) {

            return response()->json([

                'status' => 200, 

                'data' => $vehicles->map(function ($vehicle) {

                    return $vehicle->name; 

                })

            ]);

        } else {

            return response()->json(['status' => 404, 'message' => "Data Not Found"]);

        }



    } catch (Exception $e) {

        return response()->json(['status' => 500, 'error' => $e->getMessage()]);

    }

}

}

