<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = "driver";

    protected $fillable = [
        'driver_no',
        'name',
        'email',
        'phone',
        'password',
        'vech_type',
        'photo',
        'status',
        'address',
        'commission_val',
        'booking_comm_val',
        'booking_email',
        'ni_num',
        'vech_reg_num',
        'vech_color',
        'make',
        'model',
        'no_seat',
        'vech_insurance',
        'vech_insur_expiry_date',
        'vech_licence_no',
        'vech_lic_expiry_date',
        'pco_licence_no',
        'pco_lic_expiry_date',
        'driver_licence_no',
        'driver_lic_expiry_date',
        'mot_no',
        'mot_expiry_date',
        'refresh_time',
        'reminder_time',
        'gap_time',
        'customer_call',
        'dob',
        'start_date',
        'end_date',
    ];

    public function createDriver($input)
    {
        $input = (object) $input;

        // var_dump("<pre>", $input);
        // die;

        return Driver::create([
            'driver_no' => $input->driver_no,
            'name' => $input->name,
            'email' => $input->email,
            'phone' => $input->phone,
            'password' => $input->password,
            'vech_type' => $input->vehicle_type,
            'photo' => $input->profile_image_path,
            'status' => "Active",
            'address' => $input->address,
            'commission_val' => $input->commision_value,
            'booking_comm_val' => $input->driver_booking_percentage,
            'booking_email' => $input->booking_email ? $input->booking_email : '',
            'ni_num' => $input->national_insurance_no ? $input->national_insurance_no : '',
            'vech_reg_num' => $input->vehicle_reg_no,
            'vech_color' => $input->vehicle_color,
            'make' => $input->vehicle_make,
            'model' => $input->vehicle_model,
            'no_seat' => $input->number_of_seats,
            'vech_insurance' => $input->vehicle_insurance,
            'vech_insur_expiry_date' => $input->vehicle_insurance_expiry,
            'vech_licence_no' => $input->vehicle_license,
            'vech_lic_expiry_date' => $input->vehicle_license_expiry,
            'pco_licence_no' => $input->pco_license_no,
            'pco_lic_expiry_date' => $input->pco_license_no_expiry,
            'driver_licence_no' => $input->driver_license_no,
            'driver_lic_expiry_date' => $input->driver_license_no_expiry,
            'mot_no' => $input->mot_no,
            'mot_expiry_date' => $input->mot_no_expiry,
            'refresh_time' => $input->refresh_time,
            'reminder_time' => $input->before_reminder_time,
            'gap_time' => $input->start_journey_gaptime,
            'customer_call' => $input->customer_call,
            'dob' => $input->dob,
            'start_date' => $input->start_date,
            'end_date' => $input->end_date,
        ]);
    }

    public function updateDriver($id, $input)
    {
        $input = (object) $input;

        $data = [
            'driver_no' => $input->driver_no,
            'name' => $input->name ,
            'email' => $input->email,
            'phone' => $input->phone,
            'vech_type' => $input->vehicle_type,
            'photo' => $input->profile_image_path,
            'address' => $input->address,
            'commission_val' => $input->commision_value,
            'booking_comm_val' => $input->driver_booking_percentage,
            'booking_email' => $input->booking_email ? $input->booking_email : '',
            'ni_num' => $input->national_insurance_no ? $input->national_insurance_no : '',
            'vech_reg_num' => $input->vehicle_reg_no,
            'vech_color' => $input->vehicle_color,
            'make' => $input->vehicle_make,
            'model' => $input->vehicle_model,
            'no_seat' => $input->number_of_seats,
            'vech_insurance' => $input->vehicle_insurance,
            'vech_insur_expiry_date' => $input->vehicle_insurance_expiry,
            'vech_licence_no' => $input->vehicle_license,
            'vech_lic_expiry_date' => $input->vehicle_license_expiry,
            'pco_licence_no' => $input->pco_license_no,
            'pco_lic_expiry_date' => $input->pco_license_no_expiry,
            'driver_licence_no' => $input->driver_license_no,
            'driver_lic_expiry_date' => $input->driver_license_no_expiry,
            'mot_no' => $input->mot_no,
            'mot_expiry_date' => $input->mot_no_expiry,
            'refresh_time' => $input->refresh_time,
            'reminder_time' => $input->before_reminder_time,
            'gap_time' => $input->start_journey_gaptime,
            'customer_call' => $input->customer_call,
            'dob' => $input->dob,
            'start_date' => $input->start_date,
            'end_date' => $input->end_date,
        ];

        return Driver::findOrFail($id)->fill($data)->save();
    }
}
