<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\drawsController;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;
// use App\Http\Controllers\Api\v2\CCAvenueGateway;
use App\Models\user_register;

class doSthreeUpload extends Controller
{


  // Get User Product Cart Details
  public function doS3upload(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();
      // $transaction_id = $request->transaction_id;
      // $draw = Controller::getActiveDrawData()->content();
      // $drawData = json_decode($draw);
      // $draw_id = $drawData->data->active->draw_id ?? '';
      // $data = [];
      // Get User ID
      dd($request);


      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }




      $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
