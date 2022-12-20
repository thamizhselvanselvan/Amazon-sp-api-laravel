<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class MaintenanceModeController extends Controller
{
    public function index()
    {
        return view('admin.maintenanceMode.index');
    }

    public function MaintenanceModeOnOff(Request $request)
    {

        $mode = $request->mode == 'on' ? 'down' : 'up';
        // Artisan::call("${mode}");
    }
}
