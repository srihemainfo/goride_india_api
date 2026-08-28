<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use App\Models\Generalsetting;
use Yajra\DataTables\Facades\DataTables;

class HourlyDashboardController extends Controller
{
    public function index(Request $request){
        return view('car_rent.dashboard.index');
    }
    
}
