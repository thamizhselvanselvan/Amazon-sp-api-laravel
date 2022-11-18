<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CatalogDashboardController extends Controller
{
    public function Metrics()
    {
        $cat_dashboard_file = "Dashboard/catalog-dashboard-file.json";
        if (!Storage::exists("Dashboard/catalog-dashboard-file.json")) {
            Storage::put($cat_dashboard_file, '');
            commandExecFunc('mosh:catalog-dashboard-file');
        }
        $FileTime = date("F d Y H:i:s.", filemtime(Storage::path('Dashboard/catalog-dashboard-file.json')));
        $json_arrays = [];
        $json_arrays = json_decode(Storage::get($cat_dashboard_file));

        return view('Catalog.Dashboard.index', compact('json_arrays', 'FileTime'));
    }
    public function DashboardUpdate()
    {
        commandExecFunc('mosh:catalog-dashboard-file');
        return Redirect::back();
    }
}
