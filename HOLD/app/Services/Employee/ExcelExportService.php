<?php

namespace App\Services\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use App\Models\Employee;
use Carbon\{Carbon};
use Rap2hpoutre\FastExcel\{FastExcel, SheetCollection};

class ExcelExportService
{
    public function employee_export(Request $request)
    {
        //dd($request);
        $name = $request->name ?? null;
        $email = $request->email ?? null;
        $phone = $request->phone_no ?? null;
        $status = $request->status ?? null;

        $file_name = 'employee_report_';

        $query = Employee::query()->select(
            'emp_full_name',
            'phone',
            'email',
            'status',
        );
        if ($name) {
            $query->where('emp_full_name', 'like', "%{$request->get('name')}%");
        }
        if ($email) {
            $query->where('email', 'like', "%{$request->get('email')}%");
        }
        if ($phone) {
            $query->where('phone', 'like', "%{$request->get('phone')}%");
        }
        if ($status) {
            $query->where('status', $status);
        }

        $employee_details = $query->get();
        
        if ($employee_details->isNotEmpty()) {
            return (new FastExcel($employee_details))
                ->download($file_name .'.xlsx', function ($employee) {
                    return [
                        'Employee name' => ucwords(strtolower($employee->emp_full_name)),
                        'Phone' => $employee->phone,
                        'Email' => $employee->email,
                        'Status' => $employee->status,
                    ];
                });
        } else {
            return view('reports.no_data_message', ['report_type' => 'admin']);
        }
    }

}
