<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use App\Models\Generalsetting;

// use App\Models\{Customer, Booking};
use Yajra\DataTables\Facades\DataTables;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        
        
            
            // dd(session());
        // dd(session('redirect_url'));
        if(session('redirect_url') != '/dashboard'){
            return redirect(session('redirect_url'));
            
        }else{
            $current_date = now()->format('Y-m-d');
            $week_firstday = now()->startOfWeek()->format('Y/m/d');
            $week_lastday = now()->endOfWeek()->format('Y/m/d');
            $from_date = now()->startOfWeek()->format('d-m-Y');
            $to_date = now()->endOfWeek()->format('d-m-Y');
            
            $cur = now()->format('Y-m-d');
    // dd($cur);
            $bookings_count = DB::table('bookinfo')
                ->select(DB::raw('count(*) as total_bookings,
                                sum(case when order_status = "Pending" then 1 else 0 end) as pending_bookings,
                                sum(case when order_status = "Confirmed" then 1 else 0 end) as confirmed_bookings,
                                sum(case when order_status = "Assigned" then 1 else 0 end) as assigned_bookings,
                                sum(case when order_status = "Dispatched" then 1 else 0 end) as dispatched_bookings,
                                sum(case when order_status = "Canceled" then 1 else 0 end) as cancelled_bookings'))
                ->whereBetween('pickup_date', [$week_firstday, $week_lastday])
                ->first();
    
            $list_drivers = DB::table('driver')
                ->select('id', 'name')
                ->where('status', '=', 'Active')
                ->orderBy('name', 'ASC')
                ->get();
    
            if ($request->ajax()) {
                $data = DB::table('bookinfo')
                    ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
                    ->leftjoin('pick_up_points', 'bookinfo.id', '=', 'pick_up_points.booking_id')
                    ->whereIn('order_status', ['Pending', 'Confirmed', 'Assigned', 'Dispatched', 'Moving'])
                    ->where('bookinfo.pickup_date', '>=', $cur)
                    // ->whereRaw("CONCAT(bookinfo.pickup_date) >= CURDATE()")
                    // ->whereRaw("CONCAT(bookinfo.pickup_date) >= CONVERT_TZ(CURDATE(), 'UTC', 'Europe/London')")
                    ->select(
                        'bookinfo.*',
                        'driver.name AS driver_name',
                        'driver.driver_no',
                        'driver.vech_reg_num',
                        'driver.vech_color',
                        'driver.vech_type',
                        'driver.driver_licence_no',
                        'driver.pco_licence_no',
                        'driver.phone',
                        'driver.upload_photo',
                        'driver.make',
                        'driver.model',
                        'driver.no_seat',
                        DB::raw('GROUP_CONCAT(pick_up_points.location_name SEPARATOR " | ") as pick_up_points')
                    )
                    ->groupBy('bookinfo.id')
                    ->orderByRaw("CONCAT(bookinfo.pickup_date, ' ', bookinfo.pickup_time) ASC");
    
                return $this->Datatable($data, $request);
            }
    //   $list_car_fares = DB::table('gentral_setting')->exists();
    //   dd(DB::table('gentral_setting')->first());
    
    // if ($list_car_fares) {
        return view('home.index', compact('list_drivers', 'bookings_count', 'from_date', 'to_date'));
    // } else {
    //     return redirect('/bookingsetting');
    // }
        }
    
    }

    private function Datatable($data, $request)
    {
        
        // dd($data);
        
        return DataTables::of($data)
            ->addIndexColumn()
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
            ->editColumn('job_no', function ($data) {
                if ($data->reference_no) {
                    return $data->job_no .' / '. $data->reference_no;
                }else {
                    return $data->job_no;
                }
            })
            ->editColumn('no_pax', function ($data) {
                if ($data->passengers) {
                    return $data->passengers;
                }else {
                    return '';
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
                return date('d-m-Y', strtotime($data->pickup_date));
            })
            ->editColumn('pickup_time', function ($data) {
                return substr($data->pickup_time, 0, 5);
            })
            ->editColumn('driver_no', function ($data) {
                if($data->driver_no){
                    return $data->driver_no .' / ' . number_format($data->driver_amount,2);
                } else {
                    return '';
                }
            })
            ->editColumn('order_status', function ($data) {
                return $data->order_status;
            })
            ->addColumn('status', function ($row) {
                // dd($row);
                $status = '';

                if ($row->order_status === 'Pending') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Pending\">Pending</option> <option value=\"Confirmed\">Confirmed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($row->order_status === 'Confirmed') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Confirmed\">Confirmed</option> <option value=\"Pending\">Pending</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($row->order_status === 'Dispatched') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Dispatched\">Dispatched</option> <option value=\"Completed\">Completed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($row->order_status === 'Assigned') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Assigned\">Assigned</option> <option value=\"Dispatched\">Dispatched</option> <option value=\"Confirmed\">Confirmed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                } elseif ($row->order_status === 'Moving') {
                    $status = "<select class=\"form-control booking-status\" name=\"status\" data-previous=\"$row->order_status\" data-id=\"$row->id\"> <option value=\"Moving\">Moving</option><option value=\"Assigned\">Assigned</option> <option value=\"Dispatched\">Dispatched</option> <option value=\"Confirmed\">Confirmed</option> <option value=\"Canceled\">Cancelled</option> </select>";
                }

                return $status;
            })
            ->addColumn('action', function ($row) {
                // dd($row);
                
                $veh_det = DB::table('vehicle')->where('ref','=',$row->vech_type)->get();
                
                $btn =  '<a href="' . route('booking.edit', $row->id) . '" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a>';

                if ($row->order_status === 'Confirmed') {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendConfirmationEmail"><i class="fa fa-envelope"></i></a>';
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-job="' . $row->job_no . '" data-amount="' . $row->total . '" data-charges="' . $row->car_park_amount . '" data-car="' . $row->car_type . '" data-pickup_date="' . $row->pickup_date . '" data-is_mailed="'. $row->is_mailed .'" title="Assign Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a>';
                } elseif ($row->order_status === 'Assigned' || $row->order_status === 'Dispatched') {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a>';
                }

                $btn = $btn .'<a href="'. route('BookingStatusPdf',$row->id) .'" target="_blank" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Download PDF" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark downloadPDF"><i class="fa fa-download"></i></a>';

                if ($row->order_status === 'Dispatched' || $row->order_status === 'Assigned') {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Send SMS" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendSMS"><i class="fa fa-paper-plane"></i></a>';
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" data-jobid="' . $row->job_no . '" title="Send Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendEmail"><i class="fa fa-envelope"></i></a>';

                    $formatted_date = date('d-m-Y', strtotime($row->pickup_date));
                    $photo = $row->upload_photo ? $row->upload_photo : "";
                    $flight_ship = '';

                    if(!empty($row->pickup_flight_num)){
                        $flight_ship = $row->pickup_flight_num;
                    }elseif(!empty($row->pick_shipname)){
                        $flight_ship = $row->pick_shipname;
                    }
                    
                    $lugg_det = $veh_det[0]->luggage;
                    $hdlugg_det = $veh_det[0]->hand_luggage;
                    $chi_seat = $veh_det[0]->child;
                    $no_of_seat = $veh_det[0]->no_of_seats;
                    
                    // dd($lugg_det);

                    $btn = $btn . "<form id='$row->id' name='$row->id' style='visibility: hidden;'>
                        <input type='hidden' name='v_reg_no' value='$row->vech_reg_num'>
                        <input type='hidden' name='v_color' value='$row->vech_color'>
                        <input type='hidden' name='v_type' value='$row->car_type'>
                        <input type='hidden' name='v_model' value='$row->model'>
                        <input type='hidden' name='v_make' value='$row->make'>
                        <input type='hidden' name='d_photo' value='$photo'>
                        <input type='hidden' name='d_name' value='$row->driver_name'>
                        <input type='hidden' name='d_pco_no' value='$row->pco_licence_no'>
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
                        <input type='hidden' name='child_seat' value='$chi_seat'>
                        <input type='hidden' name='no_ttlseat' value='$no_of_seat'>
                        <input type='hidden' name='b_job' value='$row->job_no'>
                    </form>";
                }

                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
    
}
