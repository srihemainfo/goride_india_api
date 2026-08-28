<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\{Validator, DB};
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Customer\ExcelExportService;
use App\Services\Permissions\PermissionHelperService;

class CustomerController extends Controller
{
    private $module = 'CUSTOMER_MODULE';
    private $excel_export;
    private $permission;

    public function __construct()
    {
        $this->excel_export = new ExcelExportService;
        $this->permission = new PermissionHelperService;
    }


    public function index(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);

        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        if ($request->ajax()) {
            $data = DB::table('account')
                ->select('*');
            // ->whereNull('roles.deleted_at')
            // ->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('name')) {
                        $query->where('f_name', 'like', "%{$request->get('name')}%");
                    }

                    if ($request->has('email')) {
                        $query->where('email', 'like', "%{$request->get('email')}%");
                    }

                    if ($request->has('phone')) {
                        $query->where('phone', 'like', "%{$request->get('phone')}%");
                    }
                })
                ->addColumn('action', function ($row) use ($IS_UPDATABLE) {
                    $btn = '';

                    if ($IS_UPDATABLE) {
                        $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editCustomer"><i class="fa fa-edit"></i></a>';
                    }
                    //$btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteCustomer"><i class="fa fa-trash"></i></a';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customers.index', compact('IS_CREATABLE'));
    }


    public function store(Request $request)
    {
        if(isset($request->customer_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $validator = Validator::make($request->all(), [
            "first_name" => ["required"],
            'email' => ["nullable", "email", Rule::unique('account')->ignore($request->customer_id)],
            "phone" => ["required", "numeric", Rule::unique('account')->ignore($request->customer_id)],
            "address1" => ["nullable"],
            "remarks" => ["nullable"],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        } else {
            $data = Customer::updateOrCreate(
                ['id' => $request->customer_id],
                [
                    'f_name' => $request->first_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address1' => $request->address1,
                    'remark' => $request->remarks,
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }


    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $customer = Customer::find($id);
        return response()->json($customer ? ['status' => 200, 'data' => $customer] : ['status' => 400, 'data' => NULL]);
    }


    public function destroy(Customer $customer)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $customer->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }

    public function CustomerExcelExport(Request $request)
    {
        $data = [];
        return $this->excel_export->customer_export($request);
    }
}
