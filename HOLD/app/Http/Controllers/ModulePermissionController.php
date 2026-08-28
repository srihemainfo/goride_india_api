<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\{ModulePermissions,Role};
use Illuminate\Support\Facades\Http;

class ModulePermissionController extends Controller
{
    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     $data = DB::table('module_permissions')
        //         ->select('*');

        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         ->editColumn('module_name', function ($data) {
        //             return str_replace('_', ' ', $data->module_name);
        //         })
        //         ->editColumn('is_readable', function ($data) {
        //             $is_readable = $data->is_readable == "1" ? "checked" : "";
        //             return '<input data-id="' . $data->id . '"  type="checkbox" style="width: 1.2rem; height: 1.2rem;" class="mx-auto mt-2 form-check-input is_readable" value="' . $data->is_readable . '"' . $is_readable . '>';
        //         })
        //         ->editColumn('is_creatable', function ($data) {
        //             $is_creatable = $data->is_creatable == "1" ? "checked" : "";
        //             return '<input data-id="' . $data->id . '"  type="checkbox" style="width: 1.2rem; height: 1.2rem;" class="mx-auto mt-2 form-check-input is_creatable" value="' . $data->is_creatable . '"' . $is_creatable . '>';
        //         })
        //         ->editColumn('is_updatable', function ($data) {
        //             $is_updatable = $data->is_updatable == "1" ? "checked" : "";
        //             return '<input data-id="' . $data->id . '"  type="checkbox" style="width: 1.2rem; height: 1.2rem;" class="mx-auto mt-2 form-check-input is_updatable" value="' . $data->is_updatable . '"' . $is_updatable . '>';
        //         })
        //         ->editColumn('is_deletable', function ($data) {
        //             $is_deletable = $data->is_deletable == "1" ? "checked" : "";
        //             return '<input data-id="' . $data->id . '"  type="checkbox" style="width: 1.2rem; height: 1.2rem;" class="mx-auto mt-2 form-check-input is_deletable" value="' . $data->is_deletable . '"' . $is_deletable . '>';
        //         })
        //         ->addColumn('action', function ($row) {
        //             $btn = '';
        //             $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Update Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success updateRights">UPDATE</a>';
        //             return $btn;
        //         })
        //         ->rawColumns(['is_readable', 'is_creatable', 'is_updatable', 'is_deletable', 'action'])
        //         ->make(true);
        // }
        
        
        $token =  $_COOKIE['d_token'];

        $device_id = 'device_id';
        // $url = '{{env('API_URL')}}roles/role_get';
$url = '{{env('API_URL')}}roles/role_get';
// return [$token,$device_id];
        $data = [
            'token' => $token,
            'device_id' => $device_id,
        ];


        $response = Http::post($url, $data);
        // dd($response->all());
        $roles = [];
        //   return $response->json();

        if ($response->successful()) {
            

            $data = $response->json() ;
            $status = $data['status'] ?? null;
            if($status){
                $roles = $data['data'] ?? [];
            }

        } 
        
        
        // $roles = Role::whereNotNull('title')->pluck('title', 'id');

        return view('offerdays.module_permission.index', compact('roles'));
    }

    public function UpdatePermissions(Request $request)
    {
        $data = ModulePermissions::where('id', $request->id)
            ->update(
                [
                    'is_readable' => $request->is_readable,
                    'is_creatable' => $request->is_creatable,
                    'is_updatable' => $request->is_updatable,
                    'is_deletable' => $request->is_deletable,
                ]
            );

        return response()->json($data ? ['status' => 200, 'errors' => NULL] : ['status' => 400, 'errors' => NULL]);
    }
}
