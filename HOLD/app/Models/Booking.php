<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = "bookinfo";

    protected $fillable = [
        'fname',
        'email',
        'user_id',
        'driver_id',
        'from',
        'to',
        'from_lat',
        'from_long',
        'place_from',
        'place_to',
        'to_lat',
        'to_long',
        'car_type',
        'way',
        'actual_amount',
        'special_day_percentage',
        'gross',
        'total',
        'net_total',
        'pickup_date',
        'pickup_time',
        'booking_date',
        'type',
        'payment_status',
        'order_status',
        'mobile',
        'pickup_flight_num',
        'pickup_flight_from',
        'pickup_address',
        'dest_address',
        'remarks',
        'job_no',
        'driver_amount',
        'car_park_amount',
        'deduct_profit',
        'commision',
        'commision_profit',
        'driver_final',
        'settlement',
        'message',
        'passengers',
        'child_seat',
        'baggages',
        'hand_luggages',
        'booster',
        'distance',
        'duration',
        'after_landing_time',
        'firstbaby',
        'secondbaby',
        'thirdbaby',
        'pick_shipname',
        'payment_message',
        'extracharges',
        'taxamount',
        'inv_amt',
        'reference_no',
        'multi_pickup',
        'dest_shipname',
        'additional_cost_time',
    ];

    function createOneWayTrip($input)
    {
        $input = (object) $input;
        // return $input->oneway_offerprice;

        $job = Booking::create([
            'fname' => $input->client_name,
            'email' => $input->client_email,
            'mobile' => $input->client_mobile,
            'user_id' => $input->client_id,

            'way' => $input->journey_type,
            'booking_date' => date('Y-m-d', strtotime($input->booking_date)),

            'car_type' => $input->car_type,
            'passengers' => $input->passenger_count,
            'baggages' => $input->luggage_count,
            'hand_luggages' => $input->hand_luggage_count,
            'child_seat' => $input->child_seat_count,
            'firstbaby' => isset($input->baby_seat_1) && $input->child_seat_count > 0 ? $input->baby_seat_1 : '',
            'secondbaby' => isset($input->baby_seat_2) && $input->child_seat_count > 1  ? $input->baby_seat_2 : '',
            'thirdbaby' => isset($input->baby_seat_3) && $input->child_seat_count > 2 ? $input->baby_seat_3 : '',
            
            'place_from' => $input->from_place_id,
            'place_to' => $input->to_place_id,
            'from' => $input->one_way_pick_up,
            'to' => $input->one_way_drop_off,
            'from_lat' => $input->one_way_from_lati,
            'from_long' => $input->one_way_from_longi,
            'to_lat' => $input->one_way_to_lati,
            'to_long' => $input->one_way_to_longi,

            'pickup_date' => date('Y-m-d', strtotime($input->one_way_pickup_date)),
            'pickup_time' => $input->one_way_pickup_time,
            'pickup_address' => isset($input->one_way_pickup_address) ? $input->one_way_pickup_address : '',
            'dest_address' => isset($input->one_way_dropoff_address) ? $input->one_way_dropoff_address : '',

            'pickup_flight_num' => $input->is_airport_or_ship_one_way == 1 ? $input->one_way_flight_number : '',
            'pick_shipname' => $input->is_airport_or_ship_one_way == 2 ? $input->one_way_flight_number : '',
            'after_landing_time' => $input->one_way_flight_number ? $input->one_way_flight_pickup_time : '',
            'pickup_flight_from' => '',
            'dest_shipname' => isset($input->one_way_dest_ship_name) && !empty($input->one_way_dest_ship_name) ? $input->one_way_dest_ship_name : '',

            'payment_status' => $input->one_way_payment_status,
            'order_status' => $input->one_way_order_status,
            'type' => $input->one_way_payment_method,
            'distance' => $input->one_way_distance,
            'duration' => $input->one_way_travel_time,

            'gross' => $input->one_way_total_cost,
            'total' => $input->one_way_total_cost,
            'net_total' => $input->one_way_total_cost,
            'car_park_amount' => $input->one_way_extra_cost,
            'actual_amount' => $input->one_way_actual_amount,
            'special_day_percentage' => $input->one_way_special_day_percentage,
            'multi_pickup' => isset($input->pickup_points_one_way) && $input->pickup_points_one_way == "1" && isset($input->pickup_location) && count($input->pickup_location) > 0 ? "1" : "0",

            'message' => $input->one_way_message,
            'remarks' => $input->one_way_remarks,
            'payment_message' => $input->one_way_payment_message,
            'reference_no' => $input->one_way_ref_no,
            'additional_cost_time' => $input->oneway_offerprice,

            'driver_id' => 0,
            'extracharges' => 0.00,
            'taxamount' => 0.00,
            'inv_amt' => 0.00,
            'driver_amount' => 0.00,
            'deduct_profit' => 0.00,
            'commision' => 0.00,
            'commision_profit' => 0.00,
            'driver_final' => 0.00,
        ]);

        $job_no = 'EC' . $job->id;

        $updated_job = Booking::find($job->id)->update(['job_no' => $job_no]);

        // Inserting PickupPoints
        $has_pick_up_points = isset($input->pickup_points_one_way) && $input->pickup_points_one_way == "1" ? true : false;
        $location_array = isset($input->pickup_location) ? $input->pickup_location : [];

        if($has_pick_up_points && count($location_array) > 0){
            $this->insert_pickup_points($job->id, $location_array);
        }

        return $job->id && $updated_job ? ['job_id' => $job->id, 'job_no' => $job_no] : false;
    }

    function createReturnTrip($input)
    {
        $input = (object) $input;

        $job = Booking::create([
            'fname' => $input->client_name,
            'email' => $input->client_email,
            'mobile' => $input->client_mobile,
            'user_id' => $input->client_id,

            'way' => $input->journey_type,
            'booking_date' => date('Y-m-d', strtotime($input->booking_date)),

            'car_type' => $input->car_type,
            'passengers' => $input->passenger_count,
            'baggages' => $input->luggage_count,
            'hand_luggages' => $input->hand_luggage_count,
            'child_seat' => $input->child_seat_count,
            'firstbaby' => isset($input->baby_seat_1) && $input->child_seat_count > 0 ? $input->baby_seat_1 : '',
            'secondbaby' => isset($input->baby_seat_2) && $input->child_seat_count > 1  ? $input->baby_seat_2 : '',
            'thirdbaby' => isset($input->baby_seat_3) && $input->child_seat_count > 2 ? $input->baby_seat_3 : '',

            'from' => $input->return_pick_up,
            'to' => $input->return_drop_off,
            'from_lat' => $input->return_from_lati,
            'from_long' => $input->return_from_longi,
            'to_lat' => $input->return_to_lati,
            'to_long' => $input->return_to_longi,

            'pickup_date' => date('Y-m-d', strtotime($input->return_pickup_date)),
            'pickup_time' => $input->return_pickup_time,
            'pickup_address' => isset($input->return_pickup_address) ? $input->return_pickup_address : '',
            'dest_address' => isset($input->return_dropoff_address) ? $input->return_dropoff_address : '',

            'pickup_flight_num' => $input->is_airport_or_ship_return == 1 ? $input->return_flight_number : '',
            'pick_shipname' => $input->is_airport_or_ship_return == 2 ? $input->return_flight_number : '',
            'after_landing_time' => $input->return_flight_number ? $input->return_flight_pickup_time : '',
            'pickup_flight_from' => '',

            'payment_status' => $input->return_payment_status,
            'order_status' => $input->return_order_status,
            'type' => $input->return_payment_method,
            'distance' => $input->return_distance,
            'duration' => $input->return_travel_time,

            'gross' => $input->return_total_cost,
            'total' => $input->return_total_cost,
            'net_total' => $input->return_total_cost,
            'car_park_amount' => $input->return_extra_cost,
            'actual_amount' => $input->return_actual_amount,
            'special_day_percentage' => $input->return_special_day_percentage,

            'message' => $input->return_message,
            'remarks' => $input->return_remarks,
            'payment_message' => $input->return_payment_message,
            'reference_no' => $input->return_ref_no,

            'driver_id' => 0,
            'extracharges' => 0.00,
            'taxamount' => 0.00,
            'inv_amt' => 0.00,
            'driver_amount' => 0.00,
            'deduct_profit' => 0.00,
            'commision' => 0.00,
            'commision_profit' => 0.00,
            'driver_final' => 0.00,
        ]);

        $job_no = 'EC' . $job->id;

        $updated_job = Booking::find($job->id)->update(['job_no' => $job_no]);

        return $job->id && $updated_job ? ['job_id' => $job->id, 'job_no' => $job_no] : false;
    }

    function updateTrip($id, $input)
    {
        $input = (object) $input;

        $data = [
            'fname' => $input->client_name,
            'email' => $input->client_email,
            'mobile' => $input->client_mobile,
            'user_id' => $input->client_id,

            'way' => $input->journey_type,
            'booking_date' => date('Y-m-d', strtotime($input->booking_date)),

            'car_type' => $input->car_type,
            'passengers' => $input->passenger_count,
            'baggages' => $input->luggage_count,
            'hand_luggages' => $input->hand_luggage_count,
            'child_seat' => $input->child_seat_count,
            'firstbaby' => isset($input->baby_seat_1) && $input->child_seat_count > 0 ? $input->baby_seat_1 : '',
            'secondbaby' => isset($input->baby_seat_2) && $input->child_seat_count > 1  ? $input->baby_seat_2 : '',
            'thirdbaby' => isset($input->baby_seat_3) && $input->child_seat_count > 2 ? $input->baby_seat_3 : '',
            
            
            'place_from' => $input->from_place_id,
            'place_to' => $input->to_place_id,
            'from' => $input->one_way_pick_up,
            'to' => $input->one_way_drop_off,
            'from_lat' => $input->one_way_from_lati,
            'from_long' => $input->one_way_from_longi,
            'to_lat' => $input->one_way_to_lati,
            'to_long' => $input->one_way_to_longi,

            'pickup_date' => date('Y-m-d', strtotime($input->one_way_pickup_date)),
            'pickup_time' => $input->one_way_pickup_time,
            'pickup_address' => isset($input->one_way_pickup_address) ? $input->one_way_pickup_address : '',
            'dest_address' => isset($input->one_way_dropoff_address) ? $input->one_way_dropoff_address : '',

            'pickup_flight_num' => $input->is_airport_or_ship_one_way == 1 ? $input->one_way_flight_number : '',
            'pick_shipname' => $input->is_airport_or_ship_one_way == 2 ? $input->one_way_flight_number : '',
            'after_landing_time' => $input->one_way_flight_number ? $input->one_way_flight_pickup_time : '',
            'pickup_flight_from' => '',
            'dest_shipname' => isset($input->one_way_dest_ship_name) && !empty($input->one_way_dest_ship_name) ? $input->one_way_dest_ship_name : '',

            'payment_status' => $input->one_way_payment_status,
            'order_status' => $input->one_way_order_status,
            'type' => $input->one_way_payment_method,
            'distance' => $input->one_way_distance,
            'duration' => $input->one_way_travel_time,

            'gross' => $input->one_way_total_cost,
            'total' => $input->one_way_total_cost,
            'net_total' => $input->one_way_total_cost,
            'car_park_amount' => $input->one_way_extra_cost,
            'actual_amount' => $input->one_way_total_cost,
            'special_day_percentage' => $input->one_way_special_day_percentage,
            'multi_pickup' => isset($input->pickup_points_one_way) && $input->pickup_points_one_way == "1" && isset($input->pickup_location) && count($input->pickup_location) > 0 ? "1" : "0",


            'message' => $input->one_way_message,
            'remarks' => $input->one_way_remarks,
            'payment_message' => $input->one_way_payment_message,
            'reference_no' => $input->one_way_ref_no,
            'driver_amount' => isset($input->one_way_driver_amount) ? sanitize_amount_input($input->one_way_driver_amount) : 0.00,
        ];

        return Booking::findOrFail($id)->fill($data)->save();
    }

    function multiBooking($input, $job_details, $valid_addresses, $valid_coordinates)
    {
        $inserts = [];
        $last_inserted_ids = [];
        $input = (object)$input;
        $total_input = count($input->pickup_date);

        for ($i = 0; $i < $total_input; $i++) {
            $data = [
                'fname' => $job_details->fname,
                'email' => $job_details->email,
                'mobile' => $job_details->mobile,
                'user_id' => $job_details->user_id,

                'way' => 'One Way',
                'booking_date' => date('Y-m-d'),

                'car_type' => $job_details->car_type,
                'passengers' => $job_details->passengers,
                'baggages' => $job_details->baggages,
                'hand_luggages' => $job_details->hand_luggages,
                'child_seat' => $job_details->child_seat,
                'firstbaby' => $job_details->firstbaby,
                'secondbaby' => $job_details->secondbaby,
                'thirdbaby' => $job_details->thirdbaby,
                
                'place_from' => $input->multi_fplace_id[$i],
                'place_to' => $input->multi_toplace_id[$i],
                'from' => $input->pickup_location[$i],
                'to' => $input->dropoff_location[$i],
                'from_lat' => $valid_coordinates[$input->pickup_location[$i]]["lat"],
                'from_long' => $valid_coordinates[$input->pickup_location[$i]]["long"],
                'to_lat' => $valid_coordinates[$input->dropoff_location[$i]]["lat"],
                'to_long' => $valid_coordinates[$input->dropoff_location[$i]]["long"],

                'pickup_date' => date('Y-m-d', strtotime($input->pickup_date[$i])),
                'pickup_time' => $input->pickup_time[$i],
                'pickup_address' => $valid_addresses[$input->pickup_location[$i]],
                'dest_address' => $valid_addresses[$input->dropoff_location[$i]],

                'pickup_flight_num' => '',
                'pick_shipname' => '',
                'after_landing_time' => '',
                'pickup_flight_from' => '',

                'payment_status' => $job_details->payment_status,
                'order_status' => $job_details->payment_status === 'Pending' ? 'Pending' : 'Confirmed',
                'type' => $job_details->type,
                'distance' => $job_details->distance,
                'duration' => $job_details->duration,

                'gross' => $job_details->gross,
                'total' => $job_details->total,
                'net_total' => $job_details->net_total,
                'car_park_amount' => $job_details->car_park_amount,
                'actual_amount' => $job_details->actual_amount,
                'special_day_percentage' => $job_details->special_day_percentage,

                'message' => $job_details->message,
                'remarks' => $job_details->remarks,
                'payment_message' => $job_details->payment_message,

                'driver_id' => 0,
                'extracharges' => 0.00,
                'taxamount' => 0.00,
                'inv_amt' => 0.00,
                'driver_amount' => 0.00,
                'deduct_profit' => 0.00,
                'commision' => 0.00,
                'commision_profit' => 0.00,
                'driver_final' => 0.00,
            ];

            array_push($inserts, $data);
        }

        foreach ($inserts as $insert) {
            $last_inserted_ids[] = DB::table('bookinfo')->insertGetId($insert);
        }

        foreach ($last_inserted_ids as $id) {
            $job_no = 'EC' . $id;
            Booking::find($id)->update(['job_no' => $job_no]);
        }

        return !empty($last_inserted_ids) ? $last_inserted_ids : [];
    }

    public function insert_pickup_points($booking_id, $location_array){
        $location_data = array_map(function ($location) use ($booking_id) {
            return ['location_name' => $location, 'booking_id' => $booking_id];
        }, $location_array);

        return DB::table('pick_up_points')->insert($location_data);
    }
}
