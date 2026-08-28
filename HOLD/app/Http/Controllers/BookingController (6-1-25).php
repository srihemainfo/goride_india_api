<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Mail};
use App\Models\{Customer, Booking};
use Yajra\DataTables\Facades\DataTables;
use Carbon\{Carbon, CarbonPeriod};
use App\Services\Permissions\PermissionHelperService;
use Barryvdh\DomPDF\Facade\Pdf as DOMPDF;
use Illuminate\Support\Facades\Http;  // Include HTTP Client
use Illuminate\Support\Facades\Cookie;

class BookingController extends Controller
{
    private $module = 'BOOKING_MODULE';
    private $booking;
    private $permission;
    private $driver_name_urls = ['Assigned', 'Dispatched', 'Completed', 'settled'];

    public function __construct()
    {
        $this->booking = new Booking();
        $this->permission = new PermissionHelperService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (in_array($request->get('order_status'), $this->driver_name_urls)) {
                $data = DB::table('bookinfo')
                    ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
                    ->leftjoin('pick_up_points', 'bookinfo.id', '=', 'pick_up_points.booking_id')
                    ->where('order_status', '=', $request->get('order_status'))
                    ->select(
                        'bookinfo.*',
                        'driver.name AS driver_name',
                        'driver.driver_no',
                        'driver.vech_reg_num',
                        'driver.vech_color',
                        'driver.vech_type',
                        'driver.driver_licence_no',
                        'driver.photo',
                        'driver.phone',
                        DB::raw('GROUP_CONCAT(pick_up_points.location_name SEPARATOR " | ") as pick_up_points')
                    )
                    ->groupBy('bookinfo.id');
            } elseif (strtolower($request->get('order_status')) === 'all') {
                $data = DB::table('bookinfo')
                    ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
                    ->leftjoin('pick_up_points', 'bookinfo.id', '=', 'pick_up_points.booking_id')
                    ->select(
                        'bookinfo.*',
                        'driver.name AS driver_name',
                        'driver.driver_no',
                        'driver.vech_reg_num',
                        'driver.vech_color',
                        'driver.vech_type',
                        'driver.driver_licence_no',
                        'driver.photo',
                        'driver.phone',
                        DB::raw('GROUP_CONCAT(pick_up_points.location_name SEPARATOR " | ") as pick_up_points')
                    )
                    ->groupBy('bookinfo.id')
                     ->orderBy('bookinfo.pickup_date', 'DESC');
            } else {
                $data = DB::table('bookinfo')
                    ->where('order_status', '=', $request->get('order_status'))
                    ->select('bookinfo.*', DB::raw('GROUP_CONCAT(pick_up_points.location_name SEPARATOR " | ") as pick_up_points'))
                    ->leftjoin('pick_up_points', 'bookinfo.id', '=', 'pick_up_points.booking_id')
                    ->groupBy('bookinfo.id');
            }

            return $this->Datatable($data, $request, $request->get('order_status'));
        }
    }

    public function booking_tables($order_status = null, Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        //This code in below line is used for datatable ajax request purpose
        $queryed_driver_id = $request->query('driver_id') ? $request->query('driver_id') : 'undefined';

        $list_drivers = DB::table('driver')
            ->select('id', 'name')
            ->where('status', '=', 'Active')
            ->orderBy('name', 'ASC')
            ->get();

        $allowed_urls_for_driver_filter = $this->driver_name_urls;

        return view('booking.index', compact('order_status', 'list_drivers', 'allowed_urls_for_driver_filter', 'queryed_driver_id', 'IS_CREATABLE'));
    }

    public function create()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['create']);

        $list_fleets = DB::table('vehicle')
            ->select('id', 'name', 'ref')
            ->where('status', '=', 'Active')
            ->get();

        $list_places = DB::table('place')
            ->select('id', 'place')
            ->where('status', '=', 'Active')
            ->get();

        $isEditable = false;
        

        return view('booking.create', compact('list_fleets', 'list_places', 'isEditable'));
    }

    public function store(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);

        $validator_trip = Validator::make($request->all(), [
            "client_id" => ["required"],
            "client_name" => ["required"],
            "client_email" => ["nullable", "email"],
            "client_mobile" => ["required"],
            "journey_type" => ["required"],
            "booking_date" => ["required"],
            "car_type" => ["required"],
            "passenger_count" => ["required"],
            "child_seat_count" => ["required"],
            "luggage_count" => ["required"],
            "hand_luggage_count" => ["required"],
            "baby_seat_1" => ["required_if:child_seat_count,1|required_if:child_seat_count,2|required_if:child_seat_count,3"],
            "baby_seat_2" => ["required_if:child_seat_count,2|required_if:child_seat_count,3"],
            "baby_seat_3" => ["required_if:child_seat_count,3"],
        ]);

        $validator_one_way = Validator::make($request->all(), [
            "one_way_pick_up" => ["required"],
            "one_way_drop_off" => ["required"],
            "one_way_pickup_date" => ["required"],
            "one_way_pickup_time" => ["required"],
            "one_way_pickup_address" => ["nullable"],
            "one_way_dropoff_address" => ["nullable"],
            "is_airport_or_ship_one_way" => ["required"],
            "one_way_flight_date" => ["nullable"],
            "one_way_flight_time" => ["nullable"],
            "one_way_flight_pickup_time" => ["required_if:is_airport_or_ship_one_way,1"],
            "one_way_flight_number" => ["required_if:is_airport_or_ship_one_way,1,2"],
            // "one_way_flight_from" => ["required_if:is_airport_or_ship_one_way,1,2"],
            "one_way_payment_status" => ["required"],
            "one_way_payment_method" => ["required"],
            "one_way_order_status" => ["required"],
            "one_way_total_cost" => ["required"],
            "one_way_extra_cost" => ["required"],
            "one_way_distance" => ["required"],
            "one_way_travel_time" => ["required"],
            "one_way_message" => ["nullable"],
            "one_way_remarks" => ["nullable"],
            "one_way_payment_message" => ["nullable"],
            "one_way_actual_amount" => ["nullable"],
            "one_way_special_day_percentage" => ["nullable"],
            "one_way_ref_no" => ["nullable"],
            "one_way_dest_ship_name" => ["nullable"],
        ], [
            'one_way_flight_number.required_if' => $request->is_airport_or_ship_one_way == 1 ? 'The flight number field is required.' : 'The cruise name field is required.',
            // 'one_way_flight_from.required_if' => $request->is_airport_or_ship_one_way == 1 ? 'The flight from field is required.' : 'The cruise from field is required.',
        ]);

        if ($request->journey_type === 'Return') {
            $validator_return = Validator::make($request->all(), [
                "return_pick_up" => ["required"],
                "return_drop_off" => ["required"],
                "return_pickup_date" => ["required"],
                "return_pickup_time" => ["required"],
                "return_pickup_address" => ["nullable"],
                "return_dropoff_address" => ["nullable"],
                "is_airport_or_ship_return" => ["required"],
                "return_flight_date" => ["nullable"],
                "return_flight_time" => ["nullable"],
                "return_flight_pickup_time" => ["required_if:is_airport_or_ship_return,1,2"],
                "return_flight_number" => ["required_if:is_airport_or_ship_return,1,2"],
                // "return_flight_from" => ["required_if:is_airport_or_ship_return,1,2"],
                "return_payment_status" => ["required"],
                "return_payment_method" => ["required"],
                "return_order_status" => ["required"],
                "return_total_cost" => ["required"],
                "return_extra_cost" => ["required"],
                "return_distance" => ["required"],
                "return_travel_time" => ["required"],
                "return_message" => ["nullable"],
                "return_remarks" => ["nullable"],
                "return_payment_message" => ["nullable"],
                "return_actual_amount" => ["nullable"],
                "return_special_day_percentage" => ["nullable"],
                "return_ref_no" => ["nullable"],
            ], [
                'return_flight_number.required_if' => $request->is_airport_or_ship_return == 1 ? 'The flight number field is required.' : 'The cruise name field is required.',
                // 'return_flight_from.required_if' => $request->is_airport_or_ship_return == 1 ? 'The flight from field is required.' : 'The cruise from field is required.',
            ]);
            $isReturnTripValidated = $validator_return->fails() ? $validator_return->fails() : false;
        } else {
            $isReturnTripValidated = false;
        }

        if ($validator_trip->fails() || $validator_one_way->fails() || $isReturnTripValidated) {
            return response()->json([
                'status' => 400,
                'trip_errors' => $validator_trip->errors(),
                'one_way_errors' => $validator_one_way->errors(),
                'return_errors' => $isReturnTripValidated ? $validator_return->errors() : null
            ]);
        } else {
            // dd($request);
            $redirect_url = $request->one_way_order_status === 'Confirmed' ? '/booking/list/Confirmed' : '/booking/list/Pending';

            if ($request->journey_type === 'Return') {
                $one_way_status = $this->booking->createOneWayTrip($request->all());
                $return_status = $this->booking->createReturnTrip($request->all());

                if ($one_way_status['job_no'] && $return_status['job_no']) {
                    $request->session()->put('booking_status_save', 'Your One way trip ID is: ' . $one_way_status['job_no'] . ' \n Return trip ID is: ' . $return_status['job_no']);
                }

                if (isset($request->is_multi_booking_required_one_way) && !empty($request->is_multi_booking_required_one_way)) {
                    $redirect_url = '/booking/multi-booking';
                    $request->session()->put('multi_booking_id', $one_way_status['job_id']);
                } elseif (isset($request->is_multi_booking_required_return) && !empty($request->is_multi_booking_required_return)) {
                    $redirect_url = '/booking/multi-booking';
                    $request->session()->put('multi_booking_id', $return_status['job_id']);
                }

                return response()->json($one_way_status['job_no'] && $return_status['job_no'] ? ['status' => 200, 'redirect_url' => $redirect_url, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
            } else {
                $one_way_status = $this->booking->createOneWayTrip($request->all());

                if ($one_way_status['job_no']) {
                    $request->session()->put('booking_status_save', 'Your one way trip ID is: ' . $one_way_status['job_no']);
                }

                if (isset($request->is_multi_booking_required_one_way) && !empty($request->is_multi_booking_required_one_way)) {
                    $redirect_url = '/booking/multi-booking';
                    $request->session()->put('multi_booking_id', $one_way_status['job_id']);
                }

                return response()->json($one_way_status['job_no'] ? ['status' => 200, 'redirect_url' => $redirect_url, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
            }
        }
    }

    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);

        $list_fleets = DB::table('vehicle')
            ->select('id', 'name', 'ref')
            ->where('status', '=', 'Active')
            ->get();

        $list_places = DB::table('place')
            ->select('id', 'place')
            ->where('status', '=', 'Active')
            ->get();

        $driver_amount_field = $this->driver_name_urls;

        $booking_details = Booking::findOrFail($id);

        $pick_up_points = DB::table('pick_up_points')
                    ->select('booking_id', 'location_name')
                    ->where('booking_id', '=', $id)
                    ->get()
                    ->toArray();

        $isEditable = $booking_details->id ? true : false;
        

        // dd($booking_details);

        return view('booking.create', compact('list_fleets', 'list_places', 'booking_details', 'isEditable', 'driver_amount_field', 'pick_up_points'));
    }

    public function update(Request $request, $id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);

        $validator_trip = Validator::make($request->all(), [
            "client_id" => ["required"],
            "client_name" => ["required"],
            "client_email" => ["nullable", "email"],
            "client_mobile" => ["required"],
            "journey_type" => ["required"],
            "booking_date" => ["required"],
            "car_type" => ["required"],
            "passenger_count" => ["required"],
            "child_seat_count" => ["required"],
            "luggage_count" => ["required"],
            "hand_luggage_count" => ["required"],
            "baby_seat_1" => ["required_if:child_seat_count,1|required_if:child_seat_count,2|required_if:child_seat_count,3"],
            "baby_seat_2" => ["required_if:child_seat_count,2|required_if:child_seat_count,3"],
            "baby_seat_3" => ["required_if:child_seat_count,3"],
        ]);

        $validator_one_way = Validator::make($request->all(), [
            "one_way_pick_up" => ["required"],
            "one_way_drop_off" => ["required"],
            "one_way_pickup_date" => ["required"],
            "one_way_pickup_time" => ["required"],
            "one_way_pickup_address" => ["nullable"],
            "one_way_dropoff_address" => ["nullable"],
            "is_airport_or_ship_one_way" => ["required"],
            "one_way_flight_date" => ["nullable"],
            "one_way_flight_time" => ["nullable"],
            "one_way_flight_pickup_time" => ["required_if:is_airport_or_ship_one_way,1,2"],
            "one_way_flight_number" => ["required_if:is_airport_or_ship_one_way,1,2"],
            // "one_way_flight_from" => ["required_if:is_airport_or_ship_one_way,1,2"],
            "one_way_payment_status" => ["required"],
            "one_way_payment_method" => ["required"],
            "one_way_order_status" => ["required"],
            "one_way_total_cost" => ["required"],
            "one_way_extra_cost" => ["required"],
            "one_way_distance" => ["nullable"],
            "one_way_travel_time" => ["nullable"],
            "one_way_message" => ["nullable"],
            "one_way_remarks" => ["nullable"],
            "one_way_actual_amount" => ["nullable"],
            "one_way_special_day_percentage" => ["nullable"],
            "one_way_driver_amount" => ["nullable"],
            "one_way_ref_no" => ["nullable"],
            "one_way_dest_ship_name" => ["nullable"],
        ], [
            'one_way_flight_number.required_if' => $request->is_airport_or_ship_one_way == 1 ? 'The flight number field is required.' : 'The cruise name field is required.',
            // 'one_way_flight_from.required_if' => $request->is_airport_or_ship_one_way == 1 ? 'The flight from field is required.' : 'The cruise from field is required.',
        ]);

        if ($validator_trip->fails() || $validator_one_way->fails()) {
            return response()->json([
                'status' => 400,
                'trip_errors' => $validator_trip->errors(),
                'one_way_errors' => $validator_one_way->errors()
            ]);
        } else {
            //$redirect_url = $request->one_way_order_status === 'Confirmed' ? '/booking/list/Confirmed' : '/booking/list/Pending';
            $has_pick_up_points = isset($request->pickup_points_one_way) && $request->pickup_points_one_way == "1" ? true : false;
            $location_array = isset($request->pickup_location) ? $request->pickup_location : [];

            $redirect_url = '/dashboard';
            $job = $this->booking->updateTrip($id, $request->all());

            if(get_pickup_point_count($id) > 0){
                DB::table('pick_up_points')->where('booking_id', $id)->delete();
            }

            if($has_pick_up_points && count($location_array) > 0){
                $this->booking->insert_pickup_points($id, $location_array);
            }

            if ($job) {
                $request->session()->put('booking_details_update', 'Your trip ID : EC' . $id . ' is updated.');
            }

            return response()->json($job ? ['status' => 200, 'redirect_url' => $redirect_url, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }

    public function BookingStatusUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        
        // if($request->status == 'Completed'){
        
        // $booking = DB::table('bookinfo')->where('id','=',$request->id)->get();
        
        // $now = Carbon::now();
        // $now1 = Carbon::now()->format('Y-m-d');
        // $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        // $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        // $trans_note = "Transaction from $weekStartDate to $weekEndDate";
        
        // $arr = [
        //     'total' => $booking[0]->total,
        //     'car_park_amount' => $booking[0]->car_park_amount,
        //     'driver_amt' => $booking[0]->driver_amount,
        //     'driver_id' => $booking[0]->driver_id,
        //     'settle_date' => $now1,
        //     'jobid' => $booking[0]->id,
        //     'fromdate' => $weekStartDate,
        //     'todate' => $weekEndDate,
        //     'note' => $trans_note,
        //     'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        //     ];
        
        // $transaction = DB::table('transaction')->insertGetId($arr);
        
        // //   dd($transaction);
        
        // }

        $redirect_url = '/dashboard';
        $data = Booking::updateOrCreate(['id' => $request->id], ['order_status' => $request->status]);
        if ($data) {
            $request->session()->put('booking_status_update', 'Booking status changed successfully');
        }
        return  response()->json($data->id ? ['status' => 200, 'redirect_url' => $redirect_url, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }

    public function AssignOrRemoveDriver(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);

        if ($request->status === 'Assigned') {
            $validator = Validator::make($request->all(), [
                "booking_id" => ["required"],
                "status" => ["required"],
                "driver_id" => ["required"],
                "total" => ["required", "numeric"],
                "driver_amount" => ["required", "numeric"],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors(),
                ]);
            } else {
                $driver_id = $request->driver_id;
                $redirect_url = '/dashboard';
                $message = '';

                $data = Booking::updateOrCreate(
                    ['id' => $request->booking_id],
                    [
                        'order_status' => $request->status,
                        'driver_id' => $request->driver_id,
                        'driver_amount' => $request->driver_amount
                    ]
                );

                if($data->id){
                    $request->session()->put('assign_or_remove_driver', 'Driver added successfully');
                    $message = 'Booking ID ' .$request->job_no .' is assigned for you. Please accept or decline it as soon as possible.';
                    $this->SendDriverNotification($driver_id, $message);
                }

                return  response()->json($data->id ? ['status' => 200, 'redirect_url' => $redirect_url, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
            }
        } elseif ($request->status === 'Confirmed') {
            $redirect_url = '/dashboard';
            $data = Booking::updateOrCreate(['id' => $request->booking_id], ['order_status' => $request->status, 'driver_id' => $request->driver_id]);
            if ($data) {
                $request->session()->put('assign_or_remove_driver', 'Driver removed successfully');
            }
            return  response()->json($data->id ? ['status' => 200, 'redirect_url' => $redirect_url, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
        }
    }

    public function GetClients(Request $request)
    {
        $search = $request->search;

        if ($search == '') {
            $clients = Customer::orderby('f_name', 'asc')->select('id', 'f_name')->limit(6)->get();
        } else {
            $clients = Customer::orderby('f_name', 'asc')->select('id', 'f_name')->where('f_name', 'like', '%' . $search . '%')->limit(6)->get();
        }

        $response = [];
        foreach ($clients as $client) {
            $response[] = array(
                "id" => $client->id,
                "text" => $client->f_name
            );
        }
        return response()->json($response);
    }

    public function GetClientInfo(Request $request)
    {
        $client_details = DB::table('account')
            ->select('*')
            ->where('id', '=', $request->id)
            ->get();
        return response()->json($client_details);
    }

    public function GetLocations(Request $request)
    {
        $search_string = $request->search;

        $locations_from_db = DB::table('area')
            ->select('area.id as area_id', 'area.area', 'area.address', 'area.city', 'area.pincode', 'place.id as place_id', 'place.place')
            ->leftjoin('place', 'place.id', '=', 'area.place_id')
            ->where(function ($query) use ($search_string) {
                if (!$search_string) {
                    $query->where([['area.area', 'LIKE', '%' . $search_string . '%'], ['area.status', '=', 'Active']])
                        ->where('area.place_id', '=', function ($query) {
                            $query->from('place')
                                ->select('id')
                                ->where('place', '=', 'Airports');
                        });
                } else {
                    $query->where([['area.area', 'LIKE', '%' . $search_string . '%'], ['area.status', '=', 'Active']]);
                }
            })
            ->limit(10)
            ->get();
            
            
            // dd($locations_from_db);

        $arr = [];

        if ($locations_from_db->isNotEmpty()) {
            $j = 0;

            foreach ($locations_from_db as $item) {
                if($item->address != ''){
                    $address = ','.$item->address;
                }else{
                    $address = '';
                }
                if($item->city != ''){
                    $cty = ','.$item->city;
                }else{
                    $cty = '';
                }
                if($item->pincode != ''){
                    $pincode = ' - '.$item->pincode;
                }else{
                    $pincode = '';
                }
                $arr[] = array(
                    'id' => $item->area.$address.$cty.$pincode,
                    'label' => $j,
                    'text' => $item->area.$address.$cty.$pincode,
                    'place_id' => $item->place_id,
                    'place_type' => trim($item->place),
                    'area_address' => $item->address,
                );
                $j++;
            }
            return response()->json($arr);
        } else {
            // dd($request->all());
            return response()->json($this->GetGoogleLocations($search_string));
        }
    }

    private function GetGoogleLocations($terms)
    {
        $arr = [];
        $terms = str_replace(" ", "+", "$terms");
        // dd("https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" . $terms . "&types=geocode|establishment&components=country:uk&key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk");
        $apiKey = config('services.google.api_key');
        $data = file_get_contents("https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" . $terms . "&types=geocode|establishment&components=country:uk&key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk");
        $i = 6;
        foreach (json_decode($data)->predictions as $item) {
            // dd($item->place_id);
            // dd("https://maps.googleapis.com/maps/api/place/details/json?key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk&placeid=".$item->place_id);
            $data123 = file_get_contents("https://maps.googleapis.com/maps/api/place/details/json?key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk&placeid=".$item->place_id);
            
            $data0 = json_decode($data123, true);
            
            // dd($data0['result']['address_components'][6]['short_name']);
            
            $arr[] = array(
                'id' => $item->structured_formatting->main_text . ", " . $item->structured_formatting->secondary_text." - ".$data0['result']['address_components'][6]['short_name'],
                'label' => $i,
                'text' => $item->structured_formatting->main_text . ", " . $item->structured_formatting->secondary_text." - ".$data0['result']['address_components'][6]['short_name']
            );
            $i++;
        }

        return $arr;
    }

    public function GetQuote(Request $request)
    {
        //Get Trip Distance, Duration, Latitude and Longitude
        $trip_details = $this->GetDistanceAndDuration($request->from_area, $request->to_area);
        //Get Trip Fare
        $trip_fare = $this->GetFare($request->car_type, $trip_details['miles']);
        $trip_details['total_fare'] = current($trip_fare)->total_fare;
        
        // dd($trip_details);

        return response()->json($trip_details);
    }

    public function GetDistanceAndDuration($from_area, $to_area)
    {
      //  dd($from_area, $to_area);

        $from_area = str_replace('-', '', $from_area);
        $from_area = str_replace(' ', '+', $from_area);

        $to_area = str_replace('-', '', $to_area);
        $to_area = str_replace(' ', '+', $to_area);

        $origin = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$from_area+uk&key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk&sensor=false", true));
        $destination = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$to_area+uk&key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk&sensor=false", true));

        $obj = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=$from_area+uk&destinations=$to_area+uk&key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk&sensor=false", true));

        // var_dump('<pre>', $from_area->results[0]->geometry->location->lat, $from_area->results[0]->geometry->location->lng);
        // var_dump('<pre>', $obj);
        // die;

        $value = $obj->rows;
        $duration = $value[0]->elements[0]->duration->text;
        $distance = $value[0]->elements[0]->distance->text;
        // dd($distance);
        
        $distance_number = str_replace('km', '', $distance);
        // dd($distance_number);
        //Converting KMs to Miles
        $distance_in_miles = round($distance_number * 0.621371);

        //Lati and Longi for From Area
        $from_lati = $origin->results[0]->geometry->location->lat;
        $from_longi = $origin->results[0]->geometry->location->lng;

        //Lati and Longi for To Area
        $to_lati = $destination->results[0]->geometry->location->lat;
        $to_longi = $destination->results[0]->geometry->location->lng;
      //  dd($to_longi,$from_longi);

        return ['duration' => $duration, 'miles' => $distance_in_miles, 'from_lati' => $from_lati, 'from_longi' => $from_longi, 'to_lati' => $to_lati, 'to_longi' => $to_longi];
    }

    public function GetFare($vehicle_name, $miles)
    {
        $selected_column = get_vehicle_ref($vehicle_name);

        return DB::select(
            DB::raw("SELECT SUM((end - start) * `$selected_column`) - (
                    (SELECT end - :miles1 FROM `car_fares` WHERE start <= :miles2 ORDER BY end DESC LIMIT 1) *
                    (SELECT `$selected_column` FROM `car_fares` WHERE start <= :miles3 ORDER BY end DESC LIMIT 1)
                ) AS `total_fare`
                FROM `car_fares` WHERE start <= :miles4 OR end <= :miles5"),
            [
                'miles1' => (int) $miles,
                'miles2' => (int) $miles,
                'miles3' => (int) $miles,
                'miles4' => (int) $miles,
                'miles5' => (int) $miles
            ]
        );
    }

    public function GetCarDetails(Request $request)
    {
        $car_details = DB::table('vehicle')
            ->select('passenger', 'luggage', 'hand_luggage', 'booster','child')
            ->where('name', '=', $request->car_type)
            ->get();

        return response()->json($car_details);
    }

    public function CheckSpecialDay(Request $request)
    {
        $special_day_details = DB::table('special_price')
            ->select('cost')
            ->where('dates', '=', date('Y-m-d', strtotime($request->special_date)))
            ->get();

        return response()->json($special_day_details);
    }

    public function GetDrivers(Request $request)
    {
        $list_drivers = DB::table('driver')
            ->select('driver.id', 'driver.name', 'driver.vech_type', 'vehicle.ref', 'vehicle.order')
            ->leftJoin('vehicle',  'vehicle.ref', '=', 'driver.vech_type')
            ->where(function ($query) use ($request) {
                $query->where('driver.status', '=', 'Active')
                    ->where('vehicle.order', '>=', function ($query) use ($request) {
                        $query->from('vehicle')
                            ->select('order')
                            ->where('name', '=', $request->car_type)
                            ->orWhere('ref', '=', $request->car_type);
                    });
            })
            ->orderBy('name', 'ASC')
            ->get();


        return response()->json($list_drivers);
    }

    private function SendDriverNotification($driver_id, $message){
        $fcm_url = 'https://fcm.googleapis.com/fcm/send';
        $fcm_token = get_fcm_token($driver_id);

        // dd($fcm_token);

        $notification = [
            'title' => 'New Job Alert!',
            'body' => $message
        ];

        $extra_data =[
            'message' => $notification,
            'moredata' => 'Nothing'
        ];

        $fcm_notification = [
            'to' => $fcm_token,
            'notification' => $notification,
            'data' => $extra_data
        ];

        $headers = [
            'Authorization: key=' .env('FIREBASE_API_KEY'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $fcm_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcm_notification));
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        return $result->success;
    }

    public function EmailBookingDetails(Request $request){
        $message = $request->message;
        $email = strtolower($request->email);
        //  $email = 'developer@cwd.co.in';
        $company = env('APP_NAME');
        $customer = $request->customer;
        $website = env('WEBSITE_URL');

        // dd($request->all());

        $data = [
            'email_content' => $message
        ];

        try {
            Mail::send('emails.booking_details', $data, function ($message) use($company, $customer, $email, $website){
                $message->from('info@ecminibus.co.uk'. $website, $company);
                $message->to($email, $customer);
                $message->subject($company .' - Your Booking Details');
            });

            return response()->json(['status' => 200, 'message' => 'Email sent successfully.']);
        } catch (\Throwable $e) {
            dd($e);
            return response()->json(['status' => 400, 'message' => 'Failed to sent email.']);
        }

    }

    public function SMSBookingDetails(Request $request){
        $sms_customer = filter_var($request->sms_customer, FILTER_VALIDATE_BOOLEAN);
        $sms_driver = filter_var($request->sms_driver, FILTER_VALIDATE_BOOLEAN);
        $message = '';
        

        if($sms_customer){
            $customer_number = $request->customer_number;
            $customer_message = $request->customer_message;

            try {
                $result = $this->SMSBookingStatus($customer_number, $customer_message);

                if($result){
                    $message .= 'Customer SMS sent successfully.';
                } else {
                    $message .= 'Failed to sent customer SMS.';
                }
            } catch (\Throwable $e) {
                $message .= 'Failed to sent customer SMS.';
            }
        }

        if($sms_driver){
            $driver_number = $request->driver_number;
            $driver_message = $request->driver_message;

            if($sms_customer){
                $message .= ', ';
            }

            try {
                $result = $this->SMSBookingStatus($driver_number, $driver_message);

                if($result){
                    $message .= 'Customer SMS sent successfully.';
                } else {
                    $message .= 'Failed to sent customer SMS.';
                }
            } catch (\Throwable $e) {
                $message .= 'Failed to sent driver SMS.';
            }
        }

        return response()->json(['status' => 200, 'message' => $message]);
    }

    // private function SMSBookingStatus($phone_number, $message){
    //     $clockwork = new \mediaburst\ClockworkSMS\Clockwork(env('SMS_API_KEY'));
    //     $message = array('to' => $phone_number, 'message' => $message);
    //     $result = $clockwork->send($message);

    //     return isset($result['success']) && $result['success'] == 1 ? true : false;
    // }
    
    
    private function SMSBookingStatus($phone_number, $message){
        
        
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/'.env('SMS_API_KEY').'/Messages.json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'From=+447700166677&Body='.$message.'&To='.$phone_number.'',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic QUM3ZGY2Zjg1ZWE5ZDg1MmIwMDk2ZTdiZTIzMWJjMDM0MDo5NTgzYWRiZmRmMWM2ODYwZjM2NDBlOGY1ZTgzYTViMw==',
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
        

        return $response;
    }

    private function Datatable($data, $request, $order_status)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('job_no', function ($data) {
                if ($data->reference_no) {
                    return $data->job_no .' / '. $data->reference_no;
                }else {
                    return $data->job_no;
                }
            })
            ->editColumn('ship_flight', function ($data) {
                if ($data->pickup_flight_num) {
                    return $data->pickup_flight_num;
                } elseif ($data->pick_shipname) {
                    return $data->pick_shipname;
                } else {
                    return '';
                }
            })
            ->editColumn('pickup_date', function ($data) {
                return date('d-m-Y', strtotime($data->pickup_date)) . ' ' . substr($data->pickup_time, 0, 5);
            })
            /*->editColumn('booking_date', function ($data) {
                return date('d-m-Y', strtotime($data->booking_date));
            })*/
            ->filter(function ($query) use ($request) {
                $pickup_from_date = $request->has('pickup_from_date') ? date('Y-m-d', strtotime($request->get('pickup_from_date'))) : '';
                $pickup_to_date = $request->has('pickup_to_date') ? date('Y-m-d', strtotime($request->get('pickup_to_date'))) : '';

                $booking_from_date = $request->has('booking_from_date') ? date('Y-m-d', strtotime($request->get('booking_from_date'))) : '';
                $booking_to_date = $request->has('booking_to_date') ? date('Y-m-d', strtotime($request->get('booking_to_date'))) : '';

                if ($request->has('selected_driver') || $request->has('queryed_driver_id')) {
                    $driver_id = $request->get('selected_driver') ? $request->get('selected_driver') : $request->get('queryed_driver_id');

                    $query->where('driver_id', '=', "{$driver_id}");
                }

                if ($request->has('customer_name')) {
                    $query->where('fname', 'like', "%{$request->get('customer_name')}%");
                }

                if ($request->has('job_no')) {
                    $query->where('job_no', 'like', "%{$request->get('job_no')}%");
                }

                if ($request->has('ref_no')) {
                    $query->where('reference_no', 'like', "%{$request->get('ref_no')}%");
                }

                if ($pickup_from_date && $pickup_to_date) {
                    $query->whereBetween('pickup_date', [$pickup_from_date, $pickup_to_date]);
                }

                if ($booking_from_date && $booking_to_date) {
                    $query->whereBetween('booking_date', [$booking_from_date, $booking_to_date]);
                }
            })
            ->addColumn('status', function ($row) use ($order_status, $request) {
                $status = '';

                $Pending = $row->order_status === "Pending" ? "SELECTED" : "";
                $Confirmed = $row->order_status === "Confirmed" ? "SELECTED" : "";
                $Assigned = $row->order_status === "Assigned" ? "SELECTED" : "";
                $Dispatched = $row->order_status === "Dispatched" ? "SELECTED" : "";
                $Completed = $row->order_status === "Completed" ? "SELECTED" : "";
                $Settled = $row->order_status === "settled" ? "SELECTED" : "";
                $Cancelled = $row->order_status === "Canceled" ? "SELECTED" : "";

                //For individual drivers job list
                $is_status_select_disabled = $request->has('queryed_driver_id') ? 'disabled' : '';

                if ($order_status === 'Pending') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Pending\">Pending</option> <option value=\"Confirmed\">Confirmed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($order_status === 'Confirmed') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Confirmed\">Confirmed</option> <option value=\"Pending\">Pending</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($order_status === 'Assigned') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Assigned\">Assigned</option> <option value=\"Dispatched\">Dispatched</option> <option value=\"Confirmed\">Confirmed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($order_status === 'Dispatched') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Dispatched\">Dispatched</option> <option value=\"Completed\">Completed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($order_status === 'Completed') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Completed\">Completed</option> </select>";
                } elseif ($order_status === 'settled') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"settled\">Settled</option> </select>";
                } elseif ($order_status === 'Canceled') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Canceled\">Cancelled</option> <option value=\"Confirmed\">Confirmed</option> </select>";
                } elseif (strtolower($order_status) === 'all') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\" $is_status_select_disabled>
                        <option value=\"Pending\" $Pending >Pending</option>
                        <option value=\"Confirmed\" $Confirmed >Confirmed</option>
                        <option value=\"Assigned\" $Assigned >Assigned</option>
                        <option value=\"Dispatched\" $Dispatched >Dispatched</option>
                        <option value=\"Completed\" $Completed >Completed</option>
                        <option value=\"settled\" $Settled >Settled</option>
                        <option value=\"Canceled\" $Cancelled >Cancelled</option>
                    </select>";
                }

                return $status;
            })
            ->addColumn('action', function ($row) use ($order_status) {
                $btn =  '<a href="' . route('booking.edit', $row->id) . '" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a>';

                if ($order_status === 'Confirmed' || (strtolower($order_status) === 'all' && $row->order_status ==='Confirmed' )) {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendConfirmationEmail"><i class="fa fa-envelope"></i></a>';
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-job="' . $row->job_no . '" data-amount="' . $row->total . '" data-charges="' . $row->car_park_amount . '" data-car="' . $row->car_type . '" data-is_mailed="'. $row->is_mailed .'" title="Assign Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a>';
                } elseif ($order_status === 'Assigned' || $order_status === 'Dispatched' || (strtolower($order_status) === 'all' && $row->order_status ==='Assigned' ) || (strtolower($order_status) === 'all' && $row->order_status ==='Dispatched' )) {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a>';
                }

                $btn = $btn .'<a href="'. route('BookingStatusPdf',$row->id) .'" target="_blank" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Download PDF" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark downloadPDF"><i class="fa fa-download"></i></a>';

                if ($row->order_status === 'Dispatched' || $row->order_status === 'Assigned') {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Send SMS" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendSMS"><i class="fa fa-paper-plane"></i></a>';
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Send Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendEmail"><i class="fa fa-envelope"></i></a>';

                    $formatted_date = date('d-m-Y', strtotime($row->pickup_date));
                    $photo = $row->photo ? $row->photo : "";
                    $flight_ship = '';

                    if(!empty($row->pickup_flight_num)){
                        $flight_ship = $row->pickup_flight_num;
                    }elseif(!empty($row->pick_shipname)){
                        $flight_ship = $row->pick_shipname;
                    }

                    $btn = $btn . "<form id='$row->id' name='$row->id' style='visibility: hidden;'>
                        <input type='hidden' name='v_reg_no' value='$row->vech_reg_num'>
                        <input type='hidden' name='v_color' value='$row->vech_color'>
                        <input type='hidden' name='v_type' value='$row->vech_type'>
                        <input type='hidden' name='d_name' value='$row->driver_name'>
                        <input type='hidden' name='d_photo' value='$photo'>
                        <input type='hidden' name='d_phone' value='$row->phone'>
                        <input type='hidden' name='d_lic_no' value='$row->driver_licence_no'>
                        <input type='hidden' name='d_remarks' value='$row->remarks'>
                        <input type='hidden' name='c_name' value='$row->fname'>
                        <input type='hidden' name='c_phone' value='$row->mobile'>
                        <input type='hidden' name='c_email' value='$row->email'>
                        <input type='hidden' name='b_from' value='$row->from'>
                        <input type='hidden' name='b_to' value='$row->to'>
                        <input type='hidden' name='b_date' value='$formatted_date'>
                        <input type='hidden' name='b_time' value='$row->pickup_time'>
                        <input type='hidden' name='b_sf_time' value='$row->after_landing_time'>
                        <input type='hidden' name='b_amount' value='£ $row->driver_amount'>
                        <input type='hidden' name='b_pay_type' value='$row->type'>
                        <input type='hidden' name='b_seat1' value='$row->firstbaby'>
                        <input type='hidden' name='b_seat2' value='$row->secondbaby'>
                        <input type='hidden' name='b_seat3' value='$row->thirdbaby'>
                        <input type='hidden' name='b_fc_from' value='$flight_ship'>
                        <input type='hidden' name='b_fc_to' value='$row->dest_shipname'>
                        <input type='hidden' name='b_via_points' value='$row->pick_up_points'>
                        <input type='hidden' name='b_passengers' value='$row->passengers'>
                        <input type='hidden' name='b_luggage' value='$row->baggages'>
                        <input type='hidden' name='b_hand_luggage' value='$row->hand_luggages'>
                    </form>";
                }

                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function GetAllJobs(Request $request)
    {
        $search_string = $request->search;

        $jobs = DB::table('bookinfo')
            ->select('id', 'job_no')
            ->where('job_no', 'LIKE', '%' . $search_string . '%')
            ->limit(10)
            ->get();

        $arr = [];

        $j = 0;
        foreach ($jobs as $item) {
            $arr[] = array(
                'id' => $item->id,
                'label' => $j,
                'text' => $item->job_no
            );
            $j++;
        }

        return response()->json($arr);
    }

    public function GetJobDetails(Request $request)
    {
        $job_details = DB::table('bookinfo')
            ->select('*')
            ->where('id', '=', $request->id)
            ->get();
        return response()->json($job_details);
    }

    public function MultiBooking()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['create']);
        return view('booking.multi_booking_form');
    }

    public function StoreMultiBookings(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);

        $valid_from = $request->valid_from;
        $valid_to = $request->valid_to;
        $pickup_locations = $request->pickup_location;
        $dropoff_locatios = $request->dropoff_location;

        $validator_trip = Validator::make($request->all(), [
            "job_id" => ["required"],
            "valid_from" => ["required"],
            "valid_to" => ["required"],
            "pickup_date.*" => ["required"],
            "pickup_time.*" => ["required"],
            "pickup_location.*" => ["required"],
            "dropoff_location.*" => ["required"],
        ]);

        $valid_location_error = $this->isInvalidLocation([$valid_from, $valid_to], $pickup_locations, $dropoff_locatios);

        if ($validator_trip->fails() || $valid_location_error) {
            return response()->json([
                'status' => 400,
                'trip_errors' => $validator_trip->errors(),
                'valid_location_error' => $valid_location_error
            ]);
        } else {
            $job_details = DB::table('bookinfo')
                ->select('*')
                ->where('id', '=', $request->job_id)
                ->get()
                ->first();

            $valid_addresses = [$job_details->from => $job_details->pickup_address, $job_details->to => $job_details->dest_address];
            $valid_coordinates = [$job_details->from => ['lat' => $job_details->from_lat, 'long' => $job_details->from_long], $job_details->to => ['lat' => $job_details->to_lat, 'long' => $job_details->to_long]];

            $inserted_jobs = $this->booking->multiBooking($request->all(), $job_details, $valid_addresses, $valid_coordinates);

            $redirect_url = $job_details->payment_status === 'Pending' ? '/booking/list/Pending' : '/booking/list/Confirmed';

            if (!empty($inserted_jobs)) {
                $total_items = count($inserted_jobs) - 1;
                $booking_message = 'New booking ids are : ';

                foreach ($inserted_jobs as $key => $id) {
                    $booking_message .= 'EC' . $id . ($key != $total_items ? ', ' : '');
                }

                $request->session()->put('booking_status_save', $booking_message);
            }

            return response()->json(!empty($inserted_jobs) ? ['status' => 200, 'redirect_url' => $redirect_url, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }

    private function isInvalidLocation($valid_locations, $pickup_locations, $dropoff_locatios)
    {
        $validator_location_errors = [];

        foreach ($pickup_locations as $location) {
            in_array($location, $valid_locations) ? null : array_push($validator_location_errors, $location);
        }

        foreach ($dropoff_locatios as $location) {
            in_array($location, $valid_locations) ? null : array_push($validator_location_errors, $location);
        }

        return empty($validator_location_errors) ? false : true;
    }

    public function RecalculateBooking(Request $request){
    //  dd($request->all());
        $car_type = $request->car_type;
        $pick_up_points = $request->pick_up_points;

        $location_count = count($pick_up_points);
        $duration_array = [];
        $total_distance = 0;

        for($i = 0; $i < ($location_count - 1); $i++){
            $next_item = $i+1;
            $details = $this->GetDistanceAndDuration($pick_up_points[$i], $pick_up_points[$next_item]);
            // dd($details);
            array_push($duration_array, $details['duration']);
            $total_distance = $total_distance + (float) $details['miles'];
        }

        $total_duration = $this->calculate_time($duration_array);
      //  dd($total_duration);
        //Get Trip Fare
        $total_fare = current($this->GetFare($car_type, $total_distance))->total_fare;

        return response()->json(["status" => 200, "total_distance" => $total_distance, "total_duration" => $total_duration, "total_fare" => $total_fare]);
    }

    private function calculate_time($duration_array){
        $total_seconds = 0;

        foreach ($duration_array as $time) {
            $total_seconds += strtotime('1970-01-01 ' . $time . ' UTC') - strtotime('1970-01-01 00:00:00 UTC');
        }

        $total_hours = floor($total_seconds / 3600);
        $remaining_minutes = round(($total_seconds % 3600) / 60);

        $hour_text = $total_hours != 1 ? 'Hrs' : 'Hr';
        $minute_text = $remaining_minutes != 1 ? 'Mins' : 'Min';

        $full_hour_text = $total_hours > 0 ? "{$total_hours} {$hour_text}" : "";
        $full_minute_text = $minute_text > 0 ? "{$remaining_minutes} {$minute_text}" : "";

        $final_text = !empty($full_hour_text) ? "{$full_hour_text} {$full_minute_text}" : "{$full_minute_text}";

        return "{$final_text}";
    }

    public function EmailBookingStatus(Request $request){
        $booking_id = $request->booking_id;

        $booking = DB::table('bookinfo')
                ->select('bookinfo.*',  DB::raw('GROUP_CONCAT(pick_up_points.location_name SEPARATOR " | ") as pick_up_points'))
                ->leftjoin('pick_up_points', 'bookinfo.id', '=', 'pick_up_points.booking_id')
                ->where('id', '=', $booking_id)
                ->groupBy('bookinfo.id')
                ->first();

        if ($booking) {
            $booking = json_decode(json_encode($booking), true);
        } else {
            abort(404);
        }

        $file_name = 'booking_status_'.$booking['job_no'].'.pdf';
        $pdf = DOMPDF::loadView('booking.booking_status_pdf', $booking)->setPaper('a4', 'portrait');

        $customer_name = $booking['fname'];
        $email = $booking['email'];
        // $email = 'developer@cwd.co.in';
        $job_no = $booking['job_no'];
        $status = $booking['order_status'];

        try {
            $mail_status = Mail::send('emails.booking_confirmation_email', $booking, function ($message) use($customer_name, $email, $job_no, $status,  $pdf, $file_name){
                $message->from('info@ecminibus.co.uk', 'EC Minibus');
                // $message->from('ecminicrm@ecminibus.info', 'EC Minibus');
                $message->to($email, $customer_name);
                $message->subject('Booking status of - '. $job_no .' - '. $status);
                $message->attachData($pdf->output(), $file_name);
            });

            if ($mail_status instanceof \Illuminate\Mail\SentMessage) {
                Booking::where('id', $booking_id)->update(['is_mailed' => '1']);
            }else{
                return response()->json(['status' => 400, 'message' => 'Failed to sent email.']);
            }

            return response()->json(['status' => 200, 'message' => 'Email sent successfully.']);
        } catch (\Throwable $e) {
            dd($e);
            return response()->json(['status' => 400, 'message' => 'Failed to sent email.']);
        }
    }

//     public function BookingStatusPdf($booking_id)
//     {
//         $url = "{{env('API_URL')}}previewbooking";
//         $booking = DB::table('bookinfo')
//                 ->select('bookinfo.*',  DB::raw('GROUP_CONCAT(pick_up_points.location_name SEPARATOR " | ") as pick_up_points'))
//                 ->leftjoin('pick_up_points', 'bookinfo.id', '=', 'pick_up_points.booking_id')
//                 ->where('id', '=', $booking_id)
//                 ->groupBy('bookinfo.id')
//                 ->first();

//         if ($booking) {
//             $booking = json_decode(json_encode($booking), true);
//         } else {
//             abort(404);
//         }

//         // dd($booking);

//         $pdf = DOMPDF::loadView('booking.booking_status_pdf', $booking)->setPaper('a4', 'portrait');
//         return $pdf->stream('booking_status_'.$booking['job_no'].'.pdf');
//     }

// }



public function BookingStatusPdf(Request $request,$booking_id)
{
    try {
        $url = "{{env('API_URL')}}previewbooking";
        
        $d_token = isset($_COOKIE['d_token']) ? $_COOKIE['d_token'] : null;
        $device_id = isset($_COOKIE['device_id']) ? $_COOKIE['device_id'] : null; 
        $formData = [
            'token' => "SOT3mGi2pxt5M8OJ0elZAiPqcYNsmZP0VGbzlGnY8wWgeS5rFD",
            'device_id' => 0,
            'book_id' => $booking_id   
        ];

        $response = Http::post($url, $formData);

        // Check if the response is successful
        if ($response->successful()) {
            $apiData = $response->json();
            $mergedData = array_merge($apiData, [
                'status' => 200,
                'isEditable' => true
            ]);

            // Pass the merged data to the view
            $pdf = DOMPDF::loadView('booking.booking_status_pdf', ['mergedData' => $mergedData])
                         ->setPaper('a4', 'portrait');

            return $pdf->stream('booking_status_'.$apiData['booking_details']['job_no'].'.pdf');
        } else {
            abort(500, 'Failed to load API data');
        }

    } catch (Exception $e) {
        abort(500, 'Error generating PDF: ' . $e->getMessage());
    }
}
}

