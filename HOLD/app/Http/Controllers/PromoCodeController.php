<?php

namespace App\Http\Controllers;

use App\Models\Promocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, DB};
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class PromoCodeController extends Controller
{
    private $module = 'PROMO_CODE_MODULE';
    private $permission;

    public function __construct()
    {
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
            $data = DB::table('promo_code')
                ->select('*');
            // ->whereNull('roles.deleted_at')
            // ->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                    $btn = '';
                    if ($IS_UPDATABLE) {
                        $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPromocode"><i class="fa fa-edit"></i></a>';
                    }
                    if ($IS_DELETABLE) {
                        $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deletePromocode"><i class="fa fa-trash"></i></a';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('promocode.index', compact('IS_CREATABLE'));
    }
    public function store(Request $request)
    {
        if(isset($request->promocode_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $messages = ["to_date.after_or_equal" => "The 'To Date' must be equal or greater than 'From Date'."];

        $validator = Validator::make($request->all(), [
            "code" => ["required"],
            "min_value" => ["required", "numeric", "min:0"],
            "max_value" => ["required", "numeric", "min:0"],
            "from_date" => ["required", "date"],
            "to_date" => ["required", "after_or_equal:from_date"],
            "type" => ["required"],
            "values" => ["required", "numeric", "min:0"],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            $data = Promocode::updateOrCreate(
                ['id' => $request->promocode_id],
                [
                    'code' => $request->code,
                    'minvalue' => $request->min_value,
                    'maxvalue' => $request->max_value,
                    'fromdate' => $request->from_date,
                    'todate' => $request->to_date,
                    'type' => $request->type,
                    'values' => $request->values,
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }

    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $promocode = Promocode::find($id);
        return response()->json($promocode ? ['status' => 200, 'data' => $promocode] : ['status' => 400, 'data' => NULL]);
    }


    public function destroy(Promocode $promocode)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $promocode->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }
}
