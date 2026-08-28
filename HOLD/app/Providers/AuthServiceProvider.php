<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
public function boot()
{
    $this->registerPolicies();

    // Check if session permissions exist and define gates
    // if (session()->has('permissions')) {
    //     $permissions = session('permissions');
    //     foreach ($permissions as $permission => $allowed) {
    //         Gate::define($permission, function () use ($allowed) {
    //             return $allowed;
    //         });
    //     }
    // }
}


}