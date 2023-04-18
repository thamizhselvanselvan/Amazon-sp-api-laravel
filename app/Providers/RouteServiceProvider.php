<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware(['web', 'maintenance_mode'])
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/admin.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/amazonInvoice.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/asin.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/b2cship.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/beo.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/buisness_api.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/buybox.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/catalog.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/company.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/geo.php'));

            Route::middleware(['web'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/inventory.php'));

            Route::middleware(['web'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/invoice.php'));

            Route::middleware(['web'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/label.php'));

            Route::middleware(['web'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/maintenenceMode.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/orders.php'));

            Route::middleware(['web', 'maintenance_mode'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/phpunit.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/pms.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/rateMaster.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/seller.php'));

            Route::middleware(['web'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/shipntrack.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/zoho.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/test.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/buybox_stores.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/pms/scheduler.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/v2/masters.php'));
            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/v2/oms.php'));
            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/v2/test-sanjay.php'));

            Route::middleware(['web', 'maintenance_mode', 'auth'])
                ->namespace($this->namespace)
                ->group(base_path('routes/v2/test-vikesh.php'));
        });
    }

    /**                                                
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
