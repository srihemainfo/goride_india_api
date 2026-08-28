<?php

namespace App\Http\Controllers;

use App\Services\Reports\{ExcelExportService, ReportsHelperService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use Barryvdh\DomPDF\Facade\Pdf as DOMPDF;
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel};
use App\Services\Permissions\PermissionHelperService;

class ReportsController extends Controller
{
    private $excel_export;
    private $helper_service;
    private $permission;

    public function __construct()
    {
        $this->excel_export = new ExcelExportService;
        $this->helper_service = new ReportsHelperService;
        $this->permission = new PermissionHelperService;
    }

    public function ManageAdminReport(Request $request)
    {
        $this->permission->check_privilege('ADMIN_REPORT_MODULE', self::ACTION_TYPE['index']);
        $month_array = $this->helper_service->getMonthArray();
        $week_array = $this->helper_service->getWeekArray();

        return view('reports.manage_admin_report', compact('month_array', 'week_array'));
    }

    public function ManageDriverReport(Request $request)
    {
        $this->permission->check_privilege('DRIVER_REPORT_MODULE', self::ACTION_TYPE['index']);
        $month_array = $this->helper_service->getMonthArray();
        $week_array = $this->helper_service->getWeekArray();
        $list_drivers = DB::table('driver')
            ->select('id', 'name')
            ->where('status', '=', 'Active')
            ->orderBy('name', 'ASC')
            ->get();

        return view('reports.manage_driver_report', compact('list_drivers', 'month_array', 'week_array'));
    }

    public function AdminGenerateReport(Request $request)
    {
        $this->permission->check_privilege('ADMIN_REPORT_MODULE', self::ACTION_TYPE['index']);
        $data = [];
        $data['report_type'] = $report_type = $request->report_type; //Monthly or Weekly

        if ($request->excel == 1 && $report_type === 'Daily') {
            return $this->excel_export->admin_daily_export($request);
        } elseif ($request->excel == 1 && ($report_type === 'Monthly' || $report_type === 'Weekly' || $report_type === 'Custom')) {
            return $this->excel_export->admin_weekly_monthly_export($request);
        }

        if ($report_type === 'Monthly') {
            $data['from'] = $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
            $data['to'] = $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
            $file_name = 'admin_monthly_report_';
        } elseif ($report_type === 'Weekly') {
            $data['from'] = $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $data['to'] = $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'admin_weekly_report_';
        } elseif ($report_type === 'Custom') {
            $data['from'] = $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
            $data['to'] = $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
            $file_name = 'admin_custom_report_';
        } elseif ($report_type === 'Daily') {
            $data['date'] = $date = Carbon::parse($request->date_filter)->format('Y-m-d');
            $file_name = 'admin_day_report_';

            $data['job_details'] = $job_details = DB::table('bookinfo')
                ->select(
                    'driver.name as driver_name',
                    'job_no',
                    'pickup_date',
                    'pickup_time',
                    'from',
                    'to',
                    'car_type',
                    'total',
                    'order_status'
                )
                ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
                ->where('pickup_date', '=', $date)
                ->get();

            $data['summary_details'] = current(DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT(bookinfo.id) as total_raids")
                )
                ->where('pickup_date', '=', $date)
                ->get()
                ->toArray());

            if ($job_details->isNotEmpty()) {
                $pdf = DOMPDF::loadView('reports.admin_day_report_pdf', $data)->setPaper('a4', 'portrait');
                return $pdf->stream($file_name . $date . '.pdf');
            } else {
                return view('reports.no_data_message', ['report_type' => 'admin']);
            }
        }

        // DB::enableQueryLog();
        $data['job_details'] = $job_details = DB::table('bookinfo')
            ->select(
                'driver.name as driver_name',
                DB::raw("COUNT(bookinfo.id) as total_raids"),
                DB::raw("FORMAT(SUM(bookinfo.gross), 2) as total_amount"),
                DB::raw("FORMAT(SUM(bookinfo.driver_amount), 2) as driver_amount"),
                DB::raw("FORMAT(SUM(bookinfo.commision_profit), 2) as total_commission"),
                DB::raw("FORMAT(SUM(bookinfo.car_park_amount), 2) as parking_charge"),
                DB::raw("FORMAT(SUM(bookinfo.driver_final), 2) as total_final_amount"),
                DB::raw("FORMAT(SUM(bookinfo.deduct_profit), 2) as profit_deduct")
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->where('order_status', '=', 'settled')
            ->whereBetween('pickup_date', [$from, $to])
            ->groupBy('bookinfo.driver_id')
            ->orderBy('total_raids', 'desc')
            ->get();

        $data['summary_details'] = current(DB::table('bookinfo')
            ->select(
                DB::raw("COUNT('bookinfo.id') as total_raids"),
                DB::raw("FORMAT(SUM(gross), 2) as total_amount"),
                DB::raw("FORMAT(SUM(driver_amount), 2) as driver_amount"),
                DB::raw("FORMAT(SUM(deduct_profit), 2) as profit_deduct"),
                DB::raw("FORMAT(SUM(commision_profit), 2) as total_commission"),
                DB::raw("FORMAT(SUM(car_park_amount), 2) as parking_charges"),
                DB::raw("FORMAT(SUM(driver_final), 2) as total_final_amount")
            )
            ->where('order_status', '=', 'settled')
            ->whereBetween('pickup_date', [$from, $to])
            ->get()
            ->toArray());

        if ($job_details->isNotEmpty()) {
            $pdf = DOMPDF::loadView('reports.admin_report_pdf', $data)->setPaper('a4', 'portrait');
            return $pdf->stream($file_name . $from . '_to_' . $to . '.pdf');
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

    public function DriverGenerateReport(Request $request)
    {
        $this->permission->check_privilege('DRIVER_REPORT_MODULE', self::ACTION_TYPE['index']);
        $data = [];
        $data['driver_id'] = $driver_id = $request->driver_id;
        $data['report_type'] = $report_type = $request->report_type; //Monthly

        if ($request->excel == 1 && $report_type === 'Daily') {
            return $this->excel_export->driver_daily_export($request);
        } elseif ($request->excel == 1 && ($report_type === 'Monthly' || $report_type === 'Weekly' || $report_type === 'Custom')) {
            return $this->excel_export->driver_weekly_monthly_export($request);
        }

        if ($report_type === 'Monthly' || $report_type === 'Custom') {
            if($report_type === 'Monthly'){
                $data['from'] = $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
                $data['to'] = $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
                $file_name = 'driver_monthly_report_';
            } elseif($report_type === 'Custom'){
                $data['from'] = $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
                $data['to'] = $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
                $file_name = 'driver_custom_report_';
            }

            $settled_job = DB::table('transaction')
                ->select('*')
                ->where('driver_id', '=', $driver_id)
                ->whereBetween('fromdate', [$from, $to])
                ->whereBetween('todate', [$from, $to])
                ->get()
                ->toArray();

            if (empty($settled_job)) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }

            $all_jobs_id_string = $this->helper_service->getJobID($settled_job);

            $all_trans_id_string = $this->helper_service->getTransID($settled_job);

            $all_jobs_id_array = explode(',', $all_jobs_id_string);

            $all_trans_id_array = explode(',', $all_trans_id_string);

            $last_trans_id = end($all_trans_id_array);

            $data['settle_history'] = DB::table('settle_history')
                ->select(
                    DB::raw("FORMAT(old_balance, 2) as old_balance"),
                    DB::raw("FORMAT(current_balance, 2) as current_balance"),
                )
                ->where('trans_id', $last_trans_id)
                ->get()
                ->first();

            $data['amount_details'] = DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT('bookinfo.id') as total_raids"),
                    DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                    DB::raw("FORMAT(SUM(commision_profit), 2) as Commission_Amount"),
                    DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                    DB::raw("FORMAT(SUM(driver_final), 2) as Final_Amount")
                )
                ->whereIn('id', $all_jobs_id_array)
                ->get()
                ->first();

            $data['job_details'] = $job_details = DB::table('bookinfo')
                ->select(
                    'job_no',
                    'pickup_date',
                    'pickup_time',
                    'from',
                    'to',
                    'car_type',
                    'gross',
                    'driver_amount',
                    'deduct_profit',
                    'car_park_amount',
                    'commision_profit',
                    'driver_final'
                )
                ->whereIn('id', $all_jobs_id_array)
                ->orderBy('pickup_date', 'ASC')
                ->orderBy('pickup_time', 'ASC')
                ->get();
        } elseif ($report_type === 'Weekly') {
            $data['from'] = $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $data['to'] = $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'driver_weekly_report_';

            $settled_job = DB::table('transaction')
                ->select('id', 'jobid')
                ->where('driver_id', '=', $driver_id)
                ->where('fromdate', '=', $from)
                ->where('todate', '=', $to)
                ->get()
                ->first();

            if (!$settled_job) {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }

            $data['settle_history'] = DB::table('settle_history')
                ->select(
                    DB::raw("FORMAT(old_balance, 2) as old_balance"),
                    DB::raw("FORMAT(current_balance, 2) as current_balance"),
                )
                ->where('trans_id', '=', $settled_job->id)
                ->get()
                ->first();

            $data['amount_details'] = DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT('bookinfo.id') as total_raids"),
                    DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                    DB::raw("FORMAT(SUM(commision_profit), 2) as Commission_Amount"),
                    DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                    DB::raw("FORMAT(SUM(driver_final), 2) as Final_Amount")
                )
                ->whereIn('id', explode(',', $settled_job->jobid))
                ->get()
                ->first();

            $data['job_details'] = $job_details = DB::table('bookinfo')
                ->select(
                    'job_no',
                    'pickup_date',
                    'pickup_time',
                    'from',
                    'to',
                    'car_type',
                    'gross',
                    'driver_amount',
                    'deduct_profit',
                    'car_park_amount',
                    'commision_profit',
                    'driver_final'
                )
                ->whereIn('id', explode(',', $settled_job->jobid))
                ->orderBy('pickup_date', 'ASC')
                ->orderBy('pickup_time', 'ASC')
                ->get();
        } elseif ($report_type === 'Daily') {
            $data['date'] = $date = Carbon::parse($request->date_filter)->format('Y-m-d');
            $file_name = 'driver_day_report_';

            $data['job_details'] = $job_details = DB::table('bookinfo')
                ->select(
                    'driver_id',
                    'driver.name as driver_name',
                    'job_no',
                    'pickup_date',
                    'pickup_time',
                    'from',
                    'to',
                    'car_type',
                    'driver_amount',
                    'order_status'
                )
                ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
                ->where('pickup_date', '=', $date)
                ->where('driver_id', '=', $driver_id)
                ->get();

            $data['summary_details'] = current(DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT(bookinfo.id) as total_raids"),
                )
                ->where('pickup_date', '=', $date)
                ->where('driver_id', '=', $driver_id)
                ->get()
                ->toArray());

            if ($job_details->isNotEmpty()) {
                $pdf = DOMPDF::loadView('reports.driver_day_report_pdf', $data)->setPaper('a4', 'portrait');
                return $pdf->stream($file_name . driver_name_for_file($driver_id) . '_' . $date . '.pdf');
            } else {
                return view('reports.no_data_message', ['report_type' => 'driver']);
            }
        }

        if ($job_details->isNotEmpty()) {
            $pdf = DOMPDF::loadView('reports.driver_report_pdf', $data)->setPaper('a4', 'portrait');
            return $pdf->stream($file_name . driver_name_for_file($driver_id) . '_' . $from . '_to_' . $to . '.pdf');
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }
}
