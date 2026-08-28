<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Employee, User};
use Illuminate\Support\Facades\{DB, Validator, Hash};
use Illuminate\Validation\{Rule, Rules};
use Yajra\DataTables\Facades\DataTables;
use App\Services\Employee\ExcelExportService;
use Rap2hpoutre\FastExcel\{FastExcel};


class RangeFareAirportController extends Controller
{
    private $excel_export;

    public function __construct()
    {
        $this->excel_export = new ExcelExportService;
    }
    
    public function index(Request $request)
    {
      
 
        $list_vehicle = DB::table('vehicle')->select('id', 'name')->get();

        return view('rangefareairport.index', compact('list_vehicle'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        
        $validator = Validator::make($request->all(), [
            "first_name" => ["required"],
            'email' => ["required", "email", Rule::unique('employees')->ignore($request->id), Rule::unique('users')->ignore($request->email)],
            "phone" => ["required", "numeric", Rule::unique('employees')->ignore($request->id)],
            "password" => ["required", 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        } else {
            $emp_data = new \stdClass;
            $user_data = new \stdClass;
            DB::transaction(function () use ($request, &$emp_data, &$user_data) {
                $input = [
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ];

                if(isset($request->is_admin)){
                    $input['is_admin'] = '1';
                }
                
                $user_data = User::Create($input);                

                $emp_data = Employee::Create(
                    [
                        'emp_full_name' => $request->first_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'user_id' => $user_data->id,
                    ]
                );
                //dd($emp_data);
            });
            //dd($emp_data);
            return response()->json($emp_data->id && $user_data->id ? ['status' => 200, 'data' => $emp_data, 'errors' => NULL] : ['status' => 400, 'data' => NULL, 'errors' => NULL]);
        }
    }

    public function edit($id)
    {
        $employee = DB::table('employees')
                ->join('users', 'users.id', '=', 'employees.user_id')
                ->select('employees.*', 'users.is_admin')
                ->where('employees.id', '=' , $id)
                ->get()
                ->first();
        //dd($employee);
        
        return response()->json($employee  ? ['status' => 200, 'data' => $employee] : ['status' => 400, 'data' => NULL]);
    }

    public function EmployeeDetailsUpdate(Request $request)
    {
       //dd($request);
        $input = [];
        $input['edit_employee_id'] = $request->edit_employee_id;
        $input['edit_user_id'] = $request->edit_user_id;
        $input['first_name'] = $request->edit_first_name;
        $input['email'] = $request->edit_email;
        $input['phone'] = $request->edit_phone;
        $input['is_admin'] = isset($request->edit_is_admin) ? '1': '0';
        //dd($input['is_admin']);

        $validator = Validator::make($input, [
            "first_name" => ["required"],
            'email' => ["required", "email", Rule::unique('employees')->ignore($input['edit_employee_id']), Rule::unique('users')->ignore($input['edit_user_id'])],
            "phone" => ["required", "numeric", Rule::unique('employees')->ignore($input['edit_employee_id'])],    
        ]);
        //dd($validator);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        } else 
        {
            $emp_data = new \stdClass;
            $user_data = new \stdClass;
            
            $trans = DB::transaction(function () use ($input, &$emp_data, &$user_data) {
                
                $emp_data = Employee::where('id', $input['edit_employee_id'])
                ->Update(
                    [
                        'emp_full_name' => $input['first_name'],
                        'phone' => $input['phone'],
                        'email' => $input['email'],
                    ]
                );
                
                $user_data = User::where('id', $input['edit_user_id'])
                ->Update(     
                    [
                        'name' => $input['first_name'],
                        'email' => $input['email'],
                        
                        'is_admin' => $input['is_admin'],
                    ]
                );
                                
                //dd($emp_data);
            });
            
            return  response()->json($emp_data && $user_data ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
            
        }
    }


    public function destroy(Employee $employee)
    {
        $data = $employee->delete();
        return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);
    }

    public function EmployeeStatusUpdate(Request $request){
        $data = Employee::updateOrCreate(['id' => $request->id], ['status' => $request->isActive]);
        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true] : ['status' => 400, 'isUpdated' => false]);
    }

    public function EmployeePasswordChange(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        } else {
            $data = User::updateOrCreate(['id' => $request->password_user_id], ['password' => Hash::make($request->password)]);
        }

        return  response()->json($data->id ? ['status' => 200, 'isUpdated' => true, 'errors' => NULL] : ['status' => 400, 'isUpdated' => false, 'errors' => NULL]);
    }

    public function EmployeeExcelExport(Request $request)
    {
        $data = [];
            return $this->excel_export->employee_export($request);
       
    }
    
    public function carfares(Request $request)
    {
      
 
        $list_vehicle = DB::table('vehicle')->select('id', 'name')->get();
        
        // dd('test');

        return view('dynamiccarfare.index', compact('list_vehicle'));
    }
    public function Firstcarfares(Request $request)
    {
      
 
        $list_vehicle = DB::table('vehicle')->select('id', 'name')->get();

        return view('dynamiccarfare.firstFare', compact('list_vehicle'));
    }
    
      public function carfaresss(Request $request)
    {
      
 
        // $list_vehicle = DB::table('vehicle')->select('id', 'name')->get();

        return view('dynamiccarfare2.index');
    }
    
    

}
