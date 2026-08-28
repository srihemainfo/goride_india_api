<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\user_register;
use App\Models\Agent;
// use Twilio\Rest\Client;
use GuzzleHttp\Client;
use App\Http\Controllers\Template\mailController;

use Auth;
use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DeleteuserController extends Controller
{
    public function delete_account(Request $request)
    {

        $user_id = auth()->user()->id; // Assuming you are using Laravel's authentication

        // Retrieve user data from the database
        $get_user_data = DB::table('user_register')
            ->where('id', $user_id)
            ->first();

        if ($get_user_data) {
            // Prepare data for deletion log
            $log_arr = array_diff((array) $get_user_data, ['delete_request' => '1']);

            $user_profile_log_arr = [
                'user_id' => $user_id,
                'changed_by' => $user_id,
                'changed_data' => json_encode($log_arr),
                'updated_datetime' => now(), // Assuming you have the current date and time
                'ip' => request()->ip(), // Get the user's IP address
            ];

            // Update the user data to set the delete_request flag
            DB::table('user_register')
                ->where('id', $user_id)
                ->update(['delete_request' => '1']);

            // Insert the deletion log
            DB::table('user_profile_activity_log')->insert($user_profile_log_arr);

            // Check if a delete request record already exists
            $usercheck = DB::table('user_delete_request')
                ->where('user_id', $user_id)
                ->first();

            // Prepare email data
            $subject = 'Account Delete Request Received!';
            $email = $get_user_data->email; // Get the user's email

            $data = [
                'name' => $get_user_data->name . ' ' . ($get_user_data->lname ?? ''),
                'accountExpiry' => now()->addDays(30)
            ];

            // Send the email using Laravel's built-in Mail functionality
            $message = mailController::DeleteUsers($data);
            $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $message);
            // Insert or update the delete request record
            if (empty(!$usercheck)) {
                $insarr = [
                    'user_id' => $user_id,
                    'deleted_by' => $user_id,
                    'deleted_at' => now(),
                    'account_expire_date' => now()->addDays(30), // 30 days from now
                ];

                DB::table('user_delete_request')->insert($insarr);
                // dd('gfdgd');
            } else {
                $updateArrLog = [
                    'user_id' => $user_id,
                    'status' => '0',
                    'deleted_by' => $user_id,
                    'deleted_at' => now(),
                    'reactivated_at' => null,
                    'account_expire_date' => now()->addDays(30), // 30 days from now
                ];

                DB::table('user_delete_request')
                    ->where('user_id', $user_id)
                    ->update($updateArrLog);
            }
            $response = [
                'status' => 'success',
                'message' => 'Your account deleted successfully',
                'data' => $user_id,
            ];
            return response($response);
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'User not found',
                'error' => 'User not found',
            ];
            return response($response);
        }
        // $get_user_data1 = DB::table('user_register')
        //         ->where('id', $user_id, 'delete_request', '1')
        //         ->first();
        //         if ($get_user_data1) {
        //             $response = [
        //                 'status' => 'failed',
        //                 'message' => 'Already deleted this account',
        //                 'error' => 'Already deleted this account',
        //             ];
        //             return response($response);
        //         }else{

        //         }
    }
}
