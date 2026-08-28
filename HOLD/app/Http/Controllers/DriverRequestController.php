<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DriverRequestController extends Controller
    {
    
    public function index(Request $request)
        {
            
            return view('driver_request.index');
        }
    }