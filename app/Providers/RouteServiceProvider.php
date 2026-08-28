<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    public const HOME = '/home';

    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
                
            Route::middleware('api')
                ->prefix('api/v2-dr')
                ->group(base_path('routes/api_dr_v2.php'));
                
            Route::middleware('api')
                ->prefix('api/v3-dr')
                ->group(base_path('routes/api_dr_v3.php'));
                
            Route::middleware('api')
                ->prefix('api/v4-dr')
                ->group(base_path('routes/api_dr_v4.php'));
            
            // Customer Enpoint
            
            Route::middleware('api')
                ->prefix('api/v1-cus')
                ->group(base_path('routes/api_customer.php'));
                
            Route::middleware('api')
                ->prefix('api/v2-cus')
                ->group(base_path('routes/api_customer_v2.php'));
                
            Route::middleware('api')
                ->prefix('api/v3-cus')
                ->group(base_path('routes/api_customer_v3.php'));
                
            Route::middleware('api')
                ->prefix('api/v4-cus')
                ->group(base_path('routes/api_customer_v4.php'));
                
            Route::middleware('api')
                ->prefix('api/v5-cus')
                ->group(base_path('routes/api_customer_v5.php'));
                
            Route::middleware('api')
            ->prefix('api/cre')
            ->group(base_path('routes/api_cre_v1.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        // });
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(800); // ⬅ increase from 60
        });
    }
}