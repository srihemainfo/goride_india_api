<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB};
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
    {
    
    public function index(Request $request)
        {
            
            return view('role.index');
        }
    }