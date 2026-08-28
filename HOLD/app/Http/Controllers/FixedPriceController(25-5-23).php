<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{Validator, DB};
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class FixedPriceController extends Controller
{
    private $module = 'FIXED_PRICE_MODULE';
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

        $list_places = DB::table('place')->select('id', 'place')
            ->where('status', '=', 'Active')
            ->get();

        $list_areas = DB::table('area')->select('id', 'area')
            ->where('status', '=', 'Active')
            ->get();

        if ($request->ajax()) {
            $data = DB::table('admin_form')
                ->select('*')
                ->where([
                    ['id', '!=', ''], ['zone', '=', '0']
                ])
                ->where(function ($query) {
                    $query->where('temp', '!=', 'yes')
                        ->orWhereNull('temp');
                });

            return $this->Datatable($data, $request);
        }

        return view('prices.fixed.index', compact('list_places', 'list_areas', 'IS_CREATABLE'));
    }

    public function store(Request $request)
    {
        if(isset($request->price_id)){
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        } else {
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'place_from' => ['required'],
                'area_from' => ['required'],
                'place_to' => ['required'],
                'area_to' => ['required', Rule::unique('admin_form')
                    ->where('area_from', $request->area_from)
                    ->where('area_to', $request->area_to)
                    ->ignore($request->price_id)],
                'saloon' => ['nullable'],
                'executive' => ['nullable'],
                'estate' => ['nullable'],
                'mpv' => ['nullable'],
                'mpv5' => ['nullable'],
                'mpv6' => ['nullable'],
                'mpv8' => ['nullable'],
                'mpv_executive' => ['nullable']
            ],
            [
                'area_to.unique' => 'Given from and to areas combination already exist.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        } else {
            $data = Price::updateOrCreate(
                ['id' => $request->price_id],
                [
                    'place_from' => $request->place_from,
                    'area_from' => $request->area_from,
                    'place_to' => $request->place_to,
                    'area_to' => $request->area_to,
                    'saloon' => sanitize_amount_input($request->saloon),
                    'executive' => sanitize_amount_input($request->executive),
                    'estate' => sanitize_amount_input($request->estate),
                    'mpv' => sanitize_amount_input($request->mpv),
                    'mpv5' => sanitize_amount_input($request->mpv5),
                    'mpv6' => sanitize_amount_input($request->mpv6),
                    'mpv8' => sanitize_amount_input($request->mpv8),
                    'mpv_executive' => sanitize_amount_input($request->mpv_executive)
                ]
            );

            return response()->json($data->id ? ['status' => 200, 'data' => $data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }

    public function edit($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit']);
        $price = Price::find($id);
        return response()->json($price ? ['status' => 200, 'data' => $price] : ['status' => 400, 'data' => NULL]);
    }

    public function destroy($id)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy']);
        $data = DB::table('admin_form')->where('id', '=', $id)->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }

    public function FixedPriceUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);
        $data = Price::updateOrCreate(
            ['id' => $request->price_id],
            [
                'saloon' => sanitize_amount_input($request->saloon),
                'executive' => sanitize_amount_input($request->executive),
                'estate' => sanitize_amount_input($request->estate),
                'mpv' => sanitize_amount_input($request->mpv),
                'mpv5' => sanitize_amount_input($request->mpv5),
                'mpv6' => sanitize_amount_input($request->mpv6),
                'mpv8' => sanitize_amount_input($request->mpv8),
                'mpv_executive' => sanitize_amount_input($request->mpv_executive)
            ]
        );
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }

    public function GetAreaForPlace($place_id)
    {
        $list_areas = DB::table('area')->select('area')
            ->where([
                ['place_id', '=', $place_id], ['status', '=', 'Active']
            ])
            ->orderBy('area', 'ASC')
            ->get();

        return response()->json(count($list_areas) > 0 ? ['status' => 200, 'data' => $list_areas] : ['status' => 404, 'data' => NULL]);
    }

    private function Datatable($data, $request)
    {
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                if ($request->has('selected_area_from')) {
                    $query->where('area_from', '=', $request->get('selected_area_from'));
                }
                if ($request->has('selected_area_to')) {
                    $query->where('area_to', '=', $request->get('selected_area_to'));
                }
            })
            ->addColumn('saloon', function ($row) use ($IS_UPDATABLE) {
                
                if ($IS_UPDATABLE) {
                    $saloon = "<div class=\"input-group\">
                                <input name=\"saloon\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->saloon\">
                        </div>";
                } else {
                        $saloon = "<div style=\"text-align: right;\">$row->saloon</div>";
                }
                return $saloon;
            })
            ->addColumn('estate', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $estate = "<div class=\"input-group\">
                                <input name=\"estate\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->estate\">
                        </div>";
                } else {
                        $estate = "<div style=\"text-align: right;\">$row->estate</div>";
                }
                return $estate;
            })
            ->addColumn('mpv', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $mpv = "<div class=\"input-group\">
                                <input name=\"mpv\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->mpv\">
                        </div>";
                } else {
                    $mpv = "<div style=\"text-align: right;\">$row->mpv</div>";
                }
                return $mpv;
            })
            ->addColumn('mpv5', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $mpv5 = "<div class=\"input-group\">
                                <input name=\"mpv5\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->mpv5\">
                        </div>";
                } else {
                    $mpv5 = "<div style=\"text-align: right;\">$row->mpv5</div>";
                }
                return $mpv5;
            })
            ->addColumn('mpv6', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $mpv6 = "<div class=\"input-group\">
                                <input name=\"mpv6\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->mpv6\">
                        </div>";
                } else {
                    $mpv6 = "<div style=\"text-align: right;\">$row->mpv6</div>";
                }
                return $mpv6;
            })
            ->addColumn('mpv8', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $mpv8 = "<div class=\"input-group\">
                                <input name=\"mpv8\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->mpv8\">
                        </div>";
                } else {
                    $mpv8 = "<div style=\"text-align: right;\">$row->mpv8</div>";
                }
                
                return $mpv8;
            })
            ->addColumn('executive', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $executive = "<div class=\"input-group\">
                                <input name=\"executive\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->executive\">
                        </div>";
                } else {
                    $executive = "<div style=\"text-align: right;\">$row->executive</div>";
                }
                return $executive;
            })
            ->addColumn('mpv_executive', function ($row) use ($IS_UPDATABLE) {
                if ($IS_UPDATABLE) {
                    $mpv_executive = "<div class=\"input-group\">
                                <input name=\"mpv_executive\" type=\"text\" class=\"form-control form-control-30 p_extra\" placeholder=\"\"  style=\"text-align: right;\" value=\"$row->mpv_executive\">
                        </div>";
                } else {
                    $mpv_executive = "<div style=\"text-align: right;\">$row->mpv_executive</div>";
                }
                return $mpv_executive;
            })
            ->addColumn('action', function ($row) use ($IS_UPDATABLE, $IS_DELETABLE) {
                $btn = '';

                if ($IS_UPDATABLE) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Update Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success updatePrice"><i class="fa fa-check"></i></a>';
                }
                if ($IS_UPDATABLE) {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a>';
                }
                if ($IS_DELETABLE) {
                    $btn = $btn . '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Delete Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger deletePrice"><i class="fa fa-trash"></i></a';
                }
                return $btn;
            })
            ->rawColumns(['saloon', 'estate', 'mpv', 'mpv5', 'mpv6', 'mpv8', 'executive', 'mpv_executive', 'action'])
            ->make(true);
    }
}
