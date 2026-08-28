<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fleet;
use App\Models\CarFare;
use Illuminate\Support\Facades\{Validator, DB};
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;

class CarRentController extends Controller
{
    private $module = 'LIST_FLEETS_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }

    public function index(Request $request)
    {
        return view('car_rent.index');
    }

    public function create(Request $request)
    {
        return view('car_rent.create');
    }
    
    public function booking_rent_car(Request $request)
    {
        return view('car_rent.rentcar_booking');
    }
    
    public function rentcar_faremanage(Request $request)
    {
        return view('car_rent.rentcar_fare_manage');
    }
}
