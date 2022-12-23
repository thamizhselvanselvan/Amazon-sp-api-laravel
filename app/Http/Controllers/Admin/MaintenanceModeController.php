<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Artisan;
use App\Models\SystemSetting\SystemSetting;

class MaintenanceModeController extends Controller
{
    public function index()
    {
        return view('admin.maintenanceMode.index');
    }

    public function MaintenanceModeOnOff(Request $request)
    {
        $mode = $request->mode == 'on' ? '1' : '0';
        $maintenance_mode = [
            'key' => 'maintenance_mode',
            'value' => $mode,
        ];
        SystemSetting::upsert($maintenance_mode, ['key_unique'], ['value']);
    }
}
