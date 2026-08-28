<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // if (session()->has('permissions')) {
        //     $permissions = session('permissions');

        //     foreach ($permissions as $permission => $allowed) {
        //         Gate::define($permission, function () use ($allowed) {
        //             return $allowed;
        //         });
        //     }
        // }
    }

    public function register()
    {
        // Optionally add any registration logic here
    }
}
