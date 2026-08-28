<?php

namespace App\Services\Permissions;

use Illuminate\Support\Facades\{DB};

class PermissionHelperService
{
    public function check_privilege($module_name, $action_type)
    {
        // if (Auth()->user()->is_admin != "1") {
            if ($action_type === 'READ') {
                $is_readable = DB::table('module_permissions')
                    ->select('is_readable')
                    ->where('module_name', '=',  $module_name)
                    ->get()
                    ->first()
                    ->is_readable;

                return $is_readable == "1" ? true : abort(403, 'You are not authorized to do this action. Contact administrator for read rights.');
            } elseif ($action_type === 'CREATE') {
                $is_creatable = DB::table('module_permissions')
                    ->select('is_creatable')
                    ->where('module_name', '=',  $module_name)
                    ->get()
                    ->first()
                    ->is_creatable;

                return $is_creatable == "1" ? true : abort(403, 'You are not authorized to do this action. Contact administrator for create rights.');
            } elseif ($action_type === 'UPDATE') {
                $is_updatable = DB::table('module_permissions')
                    ->select('is_updatable')
                    ->where('module_name', '=',  $module_name)
                    ->get()
                    ->first()
                    ->is_updatable;

                return $is_updatable == "1" ? true : abort(403, 'You are not authorized to do this action. Contact administrator for update right.');
            } elseif ($action_type === 'DELETE') {
                $is_deletable = DB::table('module_permissions')
                    ->select('is_deletable')
                    ->where('module_name', '=',  $module_name)
                    ->get()
                    ->first()
                    ->is_deletable;

                return $is_deletable == "1" ? true : abort(403, 'You are not authorized to do this action. Contact administrator for delete rights.');
            }
        // } else {
        //     return true;
        // }
    }

    public function ui_permissions($module_name)
    {
        // if (Auth()->user()->is_admin != "1") {
            $permission_list = [];
            $permissions = DB::table('module_permissions')
                ->select(
                    'is_creatable AS CREATE',
                    'is_updatable AS UPDATE',
                    'is_deletable AS DELETE'
                )
                ->where('module_name', '=',  $module_name)
                ->get()
                ->first();


            $permission_list['CREATE'] = $permissions->CREATE == '1' ? true : false;
            $permission_list['UPDATE'] = $permissions->UPDATE == '1' ? true : false;
            $permission_list['DELETE'] = $permissions->DELETE == '1' ? true : false;

            return $permission_list;
        // } else {
        //     return [
        //         'CREATE' => true,
        //         'UPDATE' => true,
        //         'DELETE' => true
        //     ];
        // }
    }
}
