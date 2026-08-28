<?php

namespace App\Services\Reports;

use App\Services\Reports\ReportsHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel, SheetCollection};

class ExcelExportService
{
    private $helper_service;

    public function __construct()
    {
        $this->helper_service = new ReportsHelperService;
    }

    public function admin_daily_export(Request $request)
    {
        $date = Carbon::parse($request->date_filter)->format('Y-m-d');
        $file_name = 'admin_day_report_';

        $job_details = DB::table('bookinfo')
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

        if ($job_details->isNotEmpty()) {
            return (new FastExcel($job_details))
                ->download($file_name . $date . '.xlsx', function ($job) {
                    return [
                        'Driver Name' => ucwords(strtolower($job->driver_name)),
                        'Job No' => $job->job_no,
                        'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
                        'From' => $job->from,
                        'To' => $job->to,
                        'Car Type' => $job->car_type,
                        'Total Amount' => $job->total,
                        'Order Status' => $job->order_status,
                    ];
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

    public function admin_weekly_monthly_export(Request $request)
    {
        $report_type = $request->report_type;

        if ($report_type === 'Monthly') {
            $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
            $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
            $file_name = 'admin_monthly_report_';
        } elseif ($report_type === 'Weekly') {
            $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
            $file_name = 'admin_weekly_report_';
        } elseif ($report_type === 'Custom') {
            $from = Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('m/d/Y', substr($request->custom_filter, -10))->format('Y-m-d');
            $file_name = 'admin_custom_report_';
        }

        $job_details = $job_details = DB::table('bookinfo')
            ->select(
                'driver.name as Driver_Name',
                DB::raw("COUNT(bookinfo.id) as Total_Raids"),
                DB::raw("FORMAT(SUM(bookinfo.gross), 2) as Total_Amount"),
                DB::raw("FORMAT(SUM(bookinfo.driver_amount), 2) as Driver_Amount"),
                DB::raw("FORMAT(SUM(bookinfo.deduct_profit), 2) as Profit_Deduct"),
                DB::raw("FORMAT(SUM(bookinfo.commision_profit), 2) as Total_Commission"),
                DB::raw("FORMAT(SUM(bookinfo.car_park_amount), 2) as Parking_Charge"),
                DB::raw("FORMAT(SUM(bookinfo.driver_final), 2) as Total_Final_Amount")
            )
            ->leftjoin('driver', 'driver.id', '=', 'bookinfo.driver_id')
            ->where('order_status', '=', 'settled')
            ->whereBetween('pickup_date', [$from, $to])
            ->groupBy('bookinfo.driver_id')
            ->orderBy('total_raids', 'desc')
            ->get();

        $summary_details = DB::table('bookinfo')
            ->select(
                DB::raw("COUNT('bookinfo.id') as Total_Raids"),
                DB::raw("FORMAT(SUM(gross), 2) as Total_Amount"),
                DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                DB::raw("FORMAT(SUM(deduct_profit), 2) as Profit_Deduct"),
                DB::raw("FORMAT(SUM(commision_profit), 2) as Total_Commission"),
                DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                DB::raw("FORMAT(SUM(driver_final), 2) as Total_Final_Amount")
            )
            ->where('order_status', '=', 'settled')
            ->whereBetween('pickup_date', [$from, $to])
            ->get();

        if ($job_details->isNotEmpty()) {
            $sheets = new SheetCollection([
                'Job Details' => $job_details,
                'Summary' => $summary_details
            ]);

            return (new FastExcel($sheets))
                ->download($file_name . $from . '_to_' . $to . '.xlsx');
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

    public function driver_daily_export(Request $request)
    {
        $driver_id = $request->driver_id;
        $date = Carbon::parse($request->date_filter)->format('Y-m-d');
        $file_name = 'driver_day_report_';

        $job_details = DB::table('bookinfo')
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

        if ($job_details->isNotEmpty()) {
            return (new FastExcel($job_details))
                ->download($file_name . driver_name_for_file($driver_id) . '_' . $date . '.xlsx', function ($job) {
                    return [
                        'Job No' => $job->job_no,
                        'Pickup Times' => date('d-m-Y', strtotime($job->pickup_date)) . ' - ' . substr($job->pickup_time, 0, 5),
                        'From' => $job->from,
                        'To' => $job->to,
                        'Car Type' => $job->car_type,
                        'Driver Amount' => $job->driver_amount,
                        'Order Status' => $job->order_status,
                    ];
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }

    public function driver_weekly_monthly_export(Request $request)
    {
        $driver_id = $request->driver_id;
        $report_type = $request->report_type; //Monthly

        if ($report_type === 'Monthly' || $report_type === 'Custom') {
            if($report_type === 'Monthly'){
                $from = (new Carbon($request->month_filter))->startOfMonth()->format('Y-m-d');
                $to =  (new Carbon($request->month_filter))->endOfMonth()->format('Y-m-d');
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

            $settle_history = DB::table('settle_history')
                ->select(
                    DB::raw("FORMAT(old_balance, 2) as old_balance"),
                    DB::raw("FORMAT(current_balance, 2) as current_balance"),
                )
                ->where('trans_id', $last_trans_id)
                ->get()
                ->first();

            $summary_details = DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT('bookinfo.id') as Total_Raids"),
                    DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                    DB::raw("FORMAT(SUM(commision_profit), 2) as Commission_Amount"),
                    DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                    DB::raw("FORMAT(SUM(driver_final), 2) as Final_Amount")
                )
                ->whereIn('id', $all_jobs_id_array)
                ->get();

            $job_details = DB::table('bookinfo')
                ->select(
                    DB::raw("job_no as Job_No"),
                    DB::raw("pickup_date as Pickup_Date"),
                    DB::raw("pickup_time as Pickup_Time"),
                    DB::raw("`from` as `From`"),
                    DB::raw("`to` as `To`"),
                    DB::raw("car_type as Car_Type"),
                    DB::raw("gross as Gross"),
                    DB::raw("driver_amount as Driver_Amount"),
                    DB::raw("deduct_profit as Deduct_Profit"),
                    DB::raw("car_park_amount as Parking_Charge"),
                    DB::raw("commision_profit as Commission"),
                    DB::raw("driver_final as Driver_Final")
                )
                ->whereIn('id', $all_jobs_id_array)
                ->orderBy('pickup_date', 'ASC')
                ->orderBy('pickup_time', 'ASC')
                ->get();
        } elseif ($report_type === 'Weekly') {
            $from = Carbon::createFromFormat('d-m-Y', substr($request->week_filter, 0, 10))->format('Y-m-d');
            $to =  Carbon::createFromFormat('d-m-Y', substr($request->week_filter, -10))->format('Y-m-d');
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

            $settle_history = DB::table('settle_history')
                ->select(
                    DB::raw("FORMAT(old_balance, 2) as old_balance"),
                    DB::raw("FORMAT(current_balance, 2) as current_balance"),
                )
                ->where('trans_id', '=', $settled_job->id)
                ->get()
                ->first();

            $summary_details = DB::table('bookinfo')
                ->select(
                    DB::raw("COUNT('bookinfo.id') as Total_Raids"),
                    DB::raw("FORMAT(SUM(driver_amount), 2) as Driver_Amount"),
                    DB::raw("FORMAT(SUM(commision_profit), 2) as Commission_Amount"),
                    DB::raw("FORMAT(SUM(car_park_amount), 2) as Parking_Charges"),
                    DB::raw("FORMAT(SUM(driver_final), 2) as Final_Amount")
                )
                ->whereIn('id', explode(',', $settled_job->jobid))
                ->get();

            $job_details = DB::table('bookinfo')
                ->select(
                    DB::raw("job_no as Job_No"),
                    DB::raw("pickup_date as Pickup_Date"),
                    DB::raw("pickup_time as Pickup_Time"),
                    DB::raw("`from` as `From`"),
                    DB::raw("`to` as `To`"),
                    DB::raw("car_type as Car_Type"),
                    DB::raw("gross as Gross"),
                    DB::raw("driver_amount as Driver_Amount"),
                    DB::raw("deduct_profit as Deduct_Profit"),
                    DB::raw("car_park_amount as Parking_Charge"),
                    DB::raw("commision_profit as Commission"),
                    DB::raw("driver_final as Driver_Final")
                )
                ->whereIn('id', explode(',', $settled_job->jobid))
                ->orderBy('pickup_date', 'ASC')
                ->orderBy('pickup_time', 'ASC')
                ->get();
        }

        if ($job_details->isNotEmpty()) {
            $summary_details->map(function ($detail) use ($settle_history) {
                $detail->Old_Balance = $settle_history->old_balance;
                $detail->Current_Balance = $settle_history->current_balance;
                return $detail;
            });

            $sheets = new SheetCollection([
                'Job Details' => $job_details,
                'Summary' => $summary_details
            ]);

            return (new FastExcel($sheets))
                ->download($file_name . driver_name_for_file($driver_id) . '_' . $from . '_to_' . $to . '.xlsx');
        } else {
            return view('reports.no_data_message', ['report_type' => 'driver']);
        }
    }
}
