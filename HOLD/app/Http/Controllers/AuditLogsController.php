<?php

namespace App\Http\Controllers;

// namespace App\Services\Permissions;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Permissions\PermissionHelperService;
use Illuminate\Support\Facades\{Validator, DB};
use Illuminate\Support\Facades\Session;


class AuditLogsController extends Controller
{
    private $module = 'AUDITLOGS_MODULE';
    private $permission;

    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }
    
    public function index(Request $request)
    {
        
      $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
           
        return view('auditLogs.index');
    }

    public function show(AuditLog $auditLog)
    {
        

        // return view('auditLogs.show', compact('auditLog'));
    }
}
