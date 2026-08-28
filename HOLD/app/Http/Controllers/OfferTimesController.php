<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, DB};
use App\Models\Offertime;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class OfferTimesController extends Controller
{
    private $module = 'OFFER_TIMES_MODULE';
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
            $data = DB::table('special_time')
                ->select('*');
            // ->whereNull('roles.deleted_at')
            // ->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                    $btn = '';
                    if ($IS_UPDATABLE) {
                        $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editOffertimes"><i class="fa fa-edit"></i></a>';
                    }
                    if ($IS_DELETABLE) {
                        $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deleteOffertimes"><i class="fa fa-trash"></i></a';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('offertimes.index', compact('IS_CREATABLE'));
    }

    public function store(Request $request)
    {
        if(isset($request->offertime_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $validator = Validator::make($request->all(), [
            "cost" => ["required", 'numeric', "gte:0"],
            "from" => ["required", 'numeric', "gte:0", "lte:24"],
            "to" => ["required", 'numeric', "gte:0", "lte:24"],
            "content" => ["required"]
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            $data = Offertime::updateOrCreate(
                ['id' => $request->offertime_id],
                [
                    'cost' => $request->cost,
                    'from' => $request->from,
                    'to' => $request->to,
                    'content' => $request->content,
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }

    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $offertimes = Offertime::find($id);
        return response()->json($offertimes ? ['status' => 200, 'data' => $offertimes] : ['status' => 400, 'data' => NULL]);
    }

    public function destroy(Offertime $offertime)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = $offertime->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }
}
