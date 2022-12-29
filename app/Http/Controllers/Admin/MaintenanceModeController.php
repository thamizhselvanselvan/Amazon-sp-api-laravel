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
        $mode = SystemSetting::where('key', 'maintenance_mode')->get('value')->toArray();
        $maintenance_mode = $mode[0]['value'];

        return view('admin.maintenanceMode.index', compact('maintenance_mode'));
    }

    public function MaintenanceModeOnOff(Request $request)
    {
        po($request->mode);
        $mode = $request->mode == 'on' ? '1' : '0';
        $maintenance_mode = [
            'key' => 'maintenance_mode',
            'value' => $mode,
        ];
        po($mode);
        SystemSetting::upsert($maintenance_mode, ['key_unique'], ['value']);
    }
}
