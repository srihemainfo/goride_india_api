<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingNotification;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class NotificationController extends Controller
{
    private $module = 'NOTIFICATION_MODULE';
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
            $data = DB::table('booking_notifications')
                ->leftjoin('driver', 'driver.id', '=', 'booking_notifications.driver_id')
                ->select('booking_notifications.*', 'driver.name as driver_name');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge badge-success">Accepted</span>';
                    } else {
                        return '<span class="badge badge-danger">Rejected</span>';
                    }
                })
                ->addColumn('action', function ($row) use($IS_UPDATABLE){
                    $btn = '';
                    if ($row->is_read == 0 && $IS_UPDATABLE) {
                        $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" title="Mark as read" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger markNotification">MARK AS READ</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('offerdays.notification.index');
    }

    public function StoreBookingNotification(Request $request)
    {
        return response()->json(['status' => 200, 'status_id' => $request->status, 'booking_id' => $request->booking_id, 'notification_count' => get_notification_counts(), 'is_new' => true]);
    }

    public function NotificationStatusUpdate(Request $request)
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['update']);

        $data = BookingNotification::updateOrCreate(['id' => $request->id], ['is_read' => '1']);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true, 'notification_count' => get_notification_counts()] : ['status' => 400, 'isUpdated' => false]);
    }
}
