<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        return [
            "driver_no" => ["required", Rule::unique('driver')->ignore($this->input('driver_id'))],
            "name" => ["required"],
            "email" => ["required", "email", Rule::unique('driver')->ignore($this->input('driver_id'))],
            "phone" => ["required", Rule::unique('driver')->ignore($this->input('driver_id'))],
            // "password" => ["required"],
            "vehicle_type" => ["required"],
            // "profile_image" => ["nullable", "mimes:jpg,jpeg,png"],

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
        ];
    }
}
