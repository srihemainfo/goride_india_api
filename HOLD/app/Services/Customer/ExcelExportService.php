<?php

namespace App\Services\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use App\Models\Customer;
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel, SheetCollection};

class ExcelExportService
{
    public function customer_export(Request $request)
    {
        //dd($request);
        $name = $request->name ?? null;
        $email = $request->email ?? null;
        $phone = $request->phone_no ?? null;

        $file_name = 'customer_report_';

        $query = Customer::query()->select(
            'f_name',
            'phone',
            'address1',
            'email',
            'remark',
            'reg_date',
            'last_booking_date',
            
        );
        if ($name) {
            $query->where('f_name', 'like', "%{$request->get('name')}%");
        }
        if ($email) {
            $query->where('email', 'like', "%{$request->get('email')}%");
        }
        if ($phone) {
            $query->where('phone', 'like', "%{$request->get('phone_no')}%");
        }

        $customer_details = $query->get();
        
        if ($customer_details->isNotEmpty()) {
            return (new FastExcel($customer_details))
                ->download($file_name .'.xlsx', function ($customer) {
                    return [
                        'Customer name' => ucwords(strtolower($customer->f_name)),
                        'Phone' => $customer->phone,
                        'Address' => $customer->address1,
                        'Email' => $customer->email,
                        'Remarks' => $customer->remark,
                        'Registered Date' => ($customer->reg_date=="" || $customer->reg_date == NULL) ? "" : date('d-m-Y', strtotime($customer->reg_date)),
                        'Last Booking Date' => ($customer->last_booking_date=="" || $customer->last_booking_date == NULL) ? "" : date('d-m-Y', strtotime($customer->last_booking_date)),
                    ];
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

}
