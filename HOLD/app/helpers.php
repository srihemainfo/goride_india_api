<?php

function sanitize_amount_input($input = null)
{
    if (!empty($input) && is_numeric($input)) {
        return $input;
    } else {
        return 0;
    }
}

function show_driver_name($driver_id = null)
{
    return \Illuminate\Support\Facades\DB::table('driver')
        ->select('name')
        ->where('id', '=', $driver_id)
        ->get()
        ->first()
        ?->name;
}

function get_first_pickup_date()
{
    return \Illuminate\Support\Facades\DB::table('bookinfo')
        ->select('pickup_date')
        ->where('pickup_date', '!=', '1970-01-01')
        ->whereNotNull('pickup_date')
        ->get()
        ->first()
        ?->pickup_date;
}

function get_place_type($area = '')
{
    return trim(\Illuminate\Support\Facades\DB::table('place')
        ->select('place')
        ->where(function ($query) use ($area) {
            $query->where('place.id', '=', function ($query) use ($area) {
                $query->from('area')
                    ->select('place_id')
                    ->where('area', '=', $area);
            });
        })
        ->get()
        ->first()
        ?->place);
}

function driver_name_for_file($driver_id = null)
{
    return str_replace(' ', '_', strtolower(show_driver_name($driver_id)));
}

function get_notification_counts()
{
    return App\Models\BookingNotification::where('is_read', '0')->count();
}

function get_driver_commission($driver_id)
{
    return \Illuminate\Support\Facades\DB::table('driver')
        ->select('commission_val')
        ->where('id', '=', $driver_id)
        ->get()
        ->first()
        ?->commission_val;
}

function is_driver_document_expired($driver_id, $pickup_date)
{
    return \Illuminate\Support\Facades\DB::table('driver')
        ->select('vech_insur_expiry_date', 'vech_lic_expiry_date', 'mot_expiry_date', 'driver_lic_expiry_date', 'pco_lic_expiry_date')
        ->where('id', '=', $driver_id)
        ->where(function ($query) use($pickup_date){
            $query->where('vech_insur_expiry_date', '<', $pickup_date)
                ->orWhere('vech_lic_expiry_date', '<', $pickup_date)
                ->orWhere('mot_expiry_date', '<', $pickup_date)
                ->orWhere('driver_lic_expiry_date', '<', $pickup_date)
                ->orWhere('pco_lic_expiry_date', '<', $pickup_date);
        })
        ->get()
        ?->first();
}

function get_fcm_token($driver_id){
    return \Illuminate\Support\Facades\DB::table('driver')
        ->select('fcm_token')
        ->where('id', '=', $driver_id)
        ->get()
        ->first()
        ?->fcm_token;
}

function generate_sid(){
    return time().bin2hex(random_bytes(15));
}

function get_vehicle_ref($vehicle_name){
    return \Illuminate\Support\Facades\DB::table('vehicle')
        ->select('ref')
        ->where('name', '=', $vehicle_name)
        ->get()
        ->first()
        ?->ref;
}

function get_persmissions(){
    return \Illuminate\Support\Facades\DB::table('module_permissions')
        ->select('module_name', 'is_readable')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->module_name => $item->is_readable === "1" ? true : false];
        })
        ->toArray();
}

function check_add_booking_permission(){
    $permission = \Illuminate\Support\Facades\DB::table('module_permissions')
            ->select('is_creatable')
            ->where('module_name', '=', 'BOOKING_MODULE')
            ->get()
            ->first()
            ?->is_creatable;

    return $permission === "1" ? true : false;
}

function get_pickup_point_count($booking_id){
    return \Illuminate\Support\Facades\DB::table('pick_up_points')->where('booking_id', $booking_id)->count();
}

if(!function_exists('ui_permissions')){
    function ui_permissions($role_id = 0)
{
    $data = \Illuminate\Support\Facades\DB::table('module_permissions')
        ->select('module_name', 'is_readable', 'is_creatable', 'is_updatable', 'is_deletable')
        ->where('role_id', $role_id)
        ->get()
        ->mapWithKeys(function ($item) {
            return [
                $item->module_name => [
                    'read' => $item->is_readable === "1",
                    'create' => $item->is_creatable === "1",
                    'update' => $item->is_updatable === "1",
                    'delete' => $item->is_deletable === "1",
                ],
            ];
        });

    return $data ? $data->toArray() : [];
}

}
