<?php

namespace App\Services\Driver;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use App\Models\{Driver};
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel, SheetCollection};

class ExcelExportService
{
    public function driver_export(Request $request)
    {
        //dd($request);
        $name = $request->name ?? null;
        $email = $request->email ?? null;
        $status = $request->status ?? null;

        $file_name = 'driver_report_';

        $query = Driver::query()->select(
            'driver_no',
            'name',
            'phone',
            'address',
            'email',
            'dob',
            'ni_num',
            'booking_comm_val',
            'commission_val',
            'booking_email',
            'start_date',
            'end_date',
            'vech_type',
            'vech_reg_num',
            'vech_color',
            'make',
            'model',
            'no_seat',
            'vech_insurance',
            'vech_insur_expiry_date',
            'vech_licence_no',
            'vech_lic_expiry_date',
            'pco_licence_no',
            'pco_lic_expiry_date',
            'driver_licence_no',
            'driver_lic_expiry_date',
            'mot_no',
            'mot_expiry_date',
        );
        if ($name) {
            $query->where('name',  'like', "%{$request->get('name')}%");
        }
        if ($email) {
            $query->where('email', 'like', "%{$request->get('email')}%");
        }
        if ($status) {
            $query->where('status', $status);
        }

        $driver_details = $query->get();
        
        if ($driver_details->isNotEmpty()) {
            return (new FastExcel($driver_details))
                ->download($file_name .'.xlsx', function ($driver) {
                    return [
                        'Driver No' => $driver->driver_no,
                        'Driver name' => ucwords(strtolower($driver->name)),
                        'Phone No' => $driver->phone,
                        'Address' => $driver->address,
                        'Email' => $driver->email,
                        'Date of Birth' => ($driver->dob=="" || $driver->dob == NULL) ? "" : date('d-m-Y', strtotime($driver->dob)),
                        'National Insurance No' => $driver->ni_num,
                        'Booking Percentage' => $driver->booking_comm_val,
                        'Commision Value' => $driver->commission_val,
                        'Booking Email' => $driver->booking_email,
                        'Start Date' => ($driver->start_date=="" || $driver->start_date == NULL) ? "" : date('d-m-Y', strtotime($driver->start_date)),
                        'End Date' => ($driver->end_date=="" || $driver->end_date == NULL) ? "" : date('d-m-Y', strtotime($driver->end_date)),
                        'Vehicle Type' => $driver->vech_type,
                        'Vehicle Reg No' => $driver->vech_reg_num,
                        'Vehicle Color' => $driver->vech_color,
                        'Vehicle Make' => $driver->make,
                        'Vehicle Model' => $driver->model,
                        'Number of Seats' => $driver->no_seat,
                        'Vehicle Insurance' => $driver->vech_insurance,
                        'Insurance Expiry on' => $driver->emavech_insur_expiry_dateil,
                        'Vehicle Licence' => $driver->vech_licence_no,
                        'Licence Expiry on' => ($driver->vech_lic_expiry_date=="" || $driver->vech_lic_expiry_date == NULL) ? "" : date('d-m-Y', strtotime($driver->vech_lic_expiry_date)),
                        'PCO License No' => $driver->pco_licence_no,
                        'PCO Expiry on' => ($driver->pco_lic_expiry_date=="" || $driver->pco_lic_expiry_date == NULL) ? "" : date('d-m-Y', strtotime($driver->pco_lic_expiry_date)),
                        'Driver Licence No' => $driver->driver_licence_no,
                        'Driver Licence Expiry on' => ($driver->driver_lic_expiry_date=="" || $driver->driver_lic_expiry_date == NULL) ? "" : date('d-m-Y', strtotime($driver->driver_lic_expiry_date)),
                        'MOT No' => $driver->mot_no,
                        'MOT Expiry on' => ($driver->mot_expiry_date=="" || $driver->mot_expiry_date == NULL) ? "" : date('d-m-Y', strtotime($driver->mot_expiry_date)),
                                    
                    ];
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

}
