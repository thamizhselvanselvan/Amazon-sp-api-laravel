<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;

class MaintenenceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $maintenance_mode = getSystemSettingsValue('maintenance_mode', 'off');
        if ($maintenance_mode == '1') {
            Auth::logout();
            return redirect('/maintenence/mode');
        }

        return $next($request);
    }
}
